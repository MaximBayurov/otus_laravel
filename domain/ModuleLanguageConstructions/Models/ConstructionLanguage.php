<?php

namespace Domain\ModuleLanguageConstructions\Models;

/**
 * Класс сущности "Реализация языковой конструкции"
 */
class ConstructionLanguage extends \App\Models\ConstructionLanguage
{

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getConstructionId(): int
    {
        return $this->construction_id;
    }

    /**
     * @return int
     */
    public function getLanguageId(): int
    {
        return $this->language_id;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

}
