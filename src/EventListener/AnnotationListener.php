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
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * 
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 * 
 */
class AnnotationListener
{
	private $reader;
	private $validator;
	private $serializer;
	private $translator;
	
	public function __construct(Reader $reader, ValidatorInterface $validator, SerializerInterface $serializer, TranslatorInterface $translator)
	{
		$this->reader = $reader;
		$this->validator = $validator;
		$this->serializer = $serializer;
		$this->translator = $translator;
	}
	
	public function onKernelController(ControllerEvent $event)
	{
		if (false === $event->isMasterRequest()) {
			return;
		}
		
		if (!$reflMethod = $this->createReflectionMethod($event->getController())) {
		    return;
		}
		
		foreach ([
		    AccessControl::class => 'onAccessControlAnnotation', 
		    RequestContent::class => 'onRequestContentAnnotation'
		] as $class => $listener) {
			if (!$annotation = $this->reader->getMethodAnnotation($reflMethod, $class)) {
				continue;
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
		    if (!$format = $request->getFormat($mimeType)) {
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
				
			case null !== $request->getRequestFormat(null):
				$error = new NotAcceptableHttpException($this->translator->trans('request.format.not_acceptable', ['%formats%' => implode(', ', $request->getAcceptableContentTypes())], 'OkaInputHandlerBundle'));
				break;
				
			default:
				$request->attributes->set('versionNumber', $annotation->getVersionNumber());
				return;
		}
		
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
				if (false === ($key = array_search($request->getContentType(), $annotation->getFormats()))) {
				    $event->setController(function(Request $request) {
				        throw new UnsupportedMediaTypeHttpException($this->translator->trans('request.format.unsupported', ['%format%' => $request->getContentType()], 'OkaInputHandlerBundle'));
				    });
			        $event->stopPropagation();
			        return;
				}
				
				$requestContent = RequestUtil::getContentFromFormat($request, $annotation->getFormats()[$key]);
			}
		}
		
		if (null === $requestContent || false === $requestContent) {
			$event->setController(function() use ($annotation) {
			    throw new BadRequestHttpException($this->translator->trans($annotation->getValidationErrorMessage(), $annotation->getTranslationParameters(), $annotation->getTranslationDomain()));
			});
			$event->stopPropagation();
			return;
		}
		
		$errors = null;
		$validationHasFailed = false;
		
		// Input validation
		if (true === $annotation->isEnableValidation()) {
			if (true === empty($requestContent)) {
				$validationHasFailed = !$annotation->isCanBeEmpty();
			} else {
				$constraints = $annotation->getConstraints();
				
				if (!$reflectionMethod = $this->createReflectionMethod($event->getController(), $constraints)) {
				    throw new \LogicException(sprintf('Invalid option(s) passed to @%s: Constraints method "%s" is not exist.', get_class($annotation), $constraints));
				}
				
				if (false === $reflectionMethod->isStatic()) {
				    throw new \LogicException(sprintf('Invalid option(s) passed to @%s: Constraints method "%s" is not static.', get_class($annotation), $constraints));
				}
				
				if ($reflectionMethod->getNumberOfParameters() > 0) {
				    throw new \LogicException(sprintf('Invalid option(s) passed to @%s: Constraints method "%s" must not have of arguments.', get_class($annotation), $constraints));
				}
				
				$reflectionMethod->setAccessible(true);
				$errors = $this->validator->validate($requestContent, $reflectionMethod->invoke(null));
				$validationHasFailed = $errors->count() > 0;
			}
		}
		
		if (false === $validationHasFailed) {
			$request->attributes->set('requestContent', $requestContent);
		} else {
		    $event->setController(function(Request $request) use ($annotation, $errors) {
				$message = $this->translator->trans($annotation->getValidationErrorMessage(), $annotation->getTranslationParameters(), $annotation->getTranslationDomain());
				
				if (!$errors instanceof ConstraintViolationListInterface) {
					throw new BadRequestHttpException($message);
				}
				$format = $request->getRequestFormat('json');
				
				return new Response($this->serializer->serialize($errors, $format, ['title' => $message]), 400, ['Content-Type' => $request->getMimeType($format)]);
			});
			$event->stopPropagation();
		}
	}
	
	private function createReflectionMethod($object, $methodName = null):? \ReflectionMethod
	{
	    $reflMethod = null;
	    
	    switch (true) {
	        case is_array($object):
	            $reflMethod = new \ReflectionMethod($object[0], $methodName ?? $object[1]);
	            break;
	            
	        case is_object($object):
	            $reflMethod = new \ReflectionMethod(get_class($object), $methodName ?? '__invoke');
	            break;
	    }
	    
	    return $reflMethod;
	}
}
