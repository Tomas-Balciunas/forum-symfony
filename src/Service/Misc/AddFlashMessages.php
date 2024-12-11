<?php

namespace App\Service\Misc;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class AddFlashMessages
{
    private FlashBagInterface $flashBag;

    public function __construct(private readonly RequestStack $requestStack)
    {
        $this->flashBag = $this->requestStack->getSession()->getFlashBag();
    }

    public function addErrorMessages(array $errors): void
    {
        foreach ($errors as $error) {
            $this->flashBag->add('error', $error);
        }
    }

    public function addErrorMessage(string $error): void
    {
        $this->flashBag->add('error', $error);
    }

//    public function addSuccessMessages(array $messages): void
//    {
//        foreach ($messages as $message) {
//            $this->flashBag->add('success', $message);
//        }
//    }

    public function addSuccessMessage(string $message): void
    {
        $this->flashBag->add('success', $message);
    }
}