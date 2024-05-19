<?php

// src/Language/Language.php
namespace TetaFramework\Language;

class Language
{
    protected $locale;
    protected $translations = [];

    public function __construct($locale = "en")
    {
        $this->locale = $locale;
        $this->loadTranslations();
        
    }
    public function setLocale($locale)
    {
        $this->locale = $locale;
        $this->loadTranslations();
    }

    public function ChangeLang($locale)
    {
        $session = new \TetaFramework\Http\Session();
        $session->set("lang",$locale);
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function fromSession()
    {
        $session = new \TetaFramework\Http\Session();
        if($session->has("lang"))
            $this->setlocale($session->get("lang"));
    }

    protected function loadTranslations()
    {
        $translationFile = __DIR__ . "/Translations/$this->locale.json";
        if (file_exists($translationFile)) {
            $this->translations = json_decode(file_get_contents($translationFile),true);
        }
    }

    public function translate($key)
    {
        return isset($this->translations[$key]) ? $this->translations[$key] : $key;
    }
    public function getAllTranslate()
    {
        return $this->translations;
    }
}
