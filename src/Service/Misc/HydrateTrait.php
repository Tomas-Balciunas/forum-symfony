<?php

namespace App\Service\Misc;

trait HydrateTrait
{
    public static function hydrate(mixed $data): self
    {
        $dto = new self();

        foreach (get_class_vars(get_class($dto)) as $name => $value) {
            $getter = 'get' . ucfirst($name);
            if (method_exists($data, $getter) && property_exists($dto, $name)) {
                $dto->$name = $data->$getter();
            }

        }

        return $dto;
    }
}