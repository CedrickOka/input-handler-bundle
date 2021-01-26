<?php

namespace Oka\InputHandlerBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Oka\InputHandlerBundle\Annotation\AccessControl;
use Oka\InputHandlerBundle\Annotation\RequestContent;
use Oka\InputHandlerBundle\Util\RequestUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * 
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 * 
 */
class AnnotationListener
{
	private $reader;
	private $validator;
	private $translator;
	
	public function __construct(Reader $reader, ValidatorInterface $validator, TranslatorInterface $translator)
	{
		$this->reader = $reader;
		$this->validator = $validator;
		$this->translator = $translator;
	}
	
	public function onKernelController(ControllerEvent $event)
	{
		if (false === $event->isMasterRequest() || false === is_array($controller = $event->getController())) {
			return;
		}
		
		$listeners = [AccessControl::class => 'onAccessControlAnnotation', RequestContent::class => 'onRequestContentAnnotation'];
		$reflMethod = new \ReflectionMethod($controller[0], $controller[1]);
		
		foreach ($listeners as $class => $listener) {
			/** @var \Oka\InputHandlerBundle\Annotation\AccessControl $annotation */
			if (!$annotation = $this->reader->getMethodAnnotation($reflMethod, $class)) {
				return;
			}
			
			$this->$listener($event, $annotation);
			
			if (true === $event->isPropagationStopped()) {
				return;
			}
		}
	}
	
	private function onAccessControlAnnotation(ControllerEvent $event, AccessControl $annotation)
	{
		$request = $event->getRequest();
		
		foreach ($request->getAcceptableContentTypes() as $mimeType) {
			if (!$format = $this->getFormat($mimeType)) {
				continue;
			}
			
			if (false === in_array($format, $annotation->getFormats(), true)) {
				continue;
			}
			
			$request->setRequestFormat($format);
			break;
		}
		
		if (true === empty($request->getAcceptableContentTypes()) || (null === $request->getRequestFormat(null) && true === in_array('*/*', $request->getAcceptableContentTypes(), true))) {
			$request->setRequestFormat($annotation->getFormats()[0]);
		}
		
		switch (false) {
			case version_compare($request->attributes->get('version'), $annotation->getVersion(), $annotation->getVersionOperator()):
				$error = new NotAcceptableHttpException($this->translator->trans('request.version.not_acceptable', ['%version%' => $request->attributes->get('version')], 'OkaInputHandlerBundle'));
				break;
				
			case strtolower($request->attributes->get('protocol')) === $annotation->getProtocol():
				$error = new NotAcceptableHttpException($this->translator->trans('request.protocol.not_acceptable', ['%protocol%' => $request->attributes->get('protocol')], 'OkaInputHandlerBundle'));
				break;
				
			case null === $request->getRequestFormat(null):
				$error = new NotAcceptableHttpException($this->translator->trans('request.format.not_acceptable', ['%formats%' => implode(', ', $request->getAcceptableContentTypes())], 'OkaInputHandlerBundle'));
				break;
				
			default:
				$request->attributes->set('versionNumber', $annotation->getVersionNumber());
				return;
		}
		
		$request->setRequestFormat(RequestUtil::getFirstAcceptableFormat($request, $annotation->getFormats()[0]));
		$event->setController(function() use ($error) {
			throw $error;
		});
		$event->stopPropagation();
	}
	
	private function onRequestContentAnnotation(ControllerEvent $event, RequestContent $annotation)
	{
		$request = $event->getRequest();
		
		if (true === $request->isMethodCacheable()) {
			$requestContent = $request->query->all();
		} else {
			if (true === empty($annotation->getFormats())) {
				$requestContent = RequestUtil::getContent($request);
			} else {
				if (false === in_array($request->getContentType(), $annotation->getFormats())) {
					$event->setController(function(Request $request, TranslatorInterface $translator) {
						throw new UnsupportedMediaTypeHttpException($translator->trans('request.format.unsupported', ['%format%' => $request->getContentType()], 'OkaInputHandlerBundle'));
					});
					$event->stopPropagation();
					return;
				}
				
				foreach ($annotation->getFormats() as $format) {
					$requestContent = RequestUtil::getContentFromFormat($request, $format);
				}
			}
		}
		
		if (null === $requestContent || false === $requestContent) {
			$event->setController(function(Request $request, TranslatorInterface $translator) use ($annotation) {
				throw new BadRequestHttpException($translator->trans($annotation->getValidationErrorMessage(), $annotation->getTranslationParameters(), $annotation->getTranslationDomain()));
			});
			$event->stopPropagation();
			return;
		}
		
		$errors = null;
		$validationHasFailed = false;
		$controller = $event->getController();
		
		// Input validation
		if (true === $annotation->isEnableValidation()) {
			if (true === empty($requestContent)) {
				$validationHasFailed = !$annotation->isCanBeEmpty();
			} else {
				$constraints = $annotation->getConstraints();
				$reflectionMethod = new \ReflectionMethod($controller[0], $constraints);
				
				if (false === $reflectionMethod->isStatic()) {
					throw new \InvalidArgumentException(sprintf('Invalid option(s) passed to @%s: Constraints method "%s" is not static.', get_class($annotation), $constraints));
				}
				
				if ($reflectionMethod->getNumberOfParameters() > 0) {
					throw new \InvalidArgumentException(sprintf('Invalid option(s) passed to @%s: Constraints method "%s" must not have of arguments.', get_class($annotation), $constraints));
				}
				
				$reflectionMethod->setAccessible(true);
				$errors = $this->validator->validate($requestContent, $reflectionMethod->invoke(null));
				$validationHasFailed = $errors->count() > 0;
			}
		}
		
		if (false === $validationHasFailed) {
			$request->attributes->set('requestContent', $requestContent);
		} else {
			$event->setController(function(Request $request, SerializerInterface $serializer, TranslatorInterface $translator) use ($annotation, $errors) {
				$message = $translator->trans($annotation->getValidationErrorMessage(), $annotation->getTranslationParameters(), $annotation->getTranslationDomain());
				
				if (!$errors instanceof ConstraintViolationListInterface) {
					throw new BadRequestHttpException($message);
				}
				
				return new Response($serializer->serialize($errors, 'json', ['title' => $message]), 400, ['Content-Type' => 'application']);
			});
			$event->stopPropagation();
		}
	}
}
