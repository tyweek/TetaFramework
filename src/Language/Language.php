<?php

// src/Language/Language.php
namespace TetaFramework\Language;

class Language
{
    // Propiedad protegida que almacena el locale actual
    protected $locale;

    // Propiedad protegida que almacena las traducciones cargadas
    protected $translations = [];

    /**
     * Constructor que inicializa la clase con un locale (por defecto "en").
     * También carga las traducciones correspondientes al locale.
     *
     * @param string $locale El locale inicial
     */
    public function __construct($locale = "en")
    {
        $this->locale = $locale;
        $this->loadTranslations(); // Carga las traducciones para el locale especificado
    }

    /**
     * Establece un nuevo locale y carga las traducciones correspondientes.
     *
     * @param string $locale El nuevo locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        $this->loadTranslations(); // Carga las traducciones para el nuevo locale
    }

    /**
     * Cambia el idioma guardándolo en la sesión.
     *
     * @param string $locale El nuevo locale
     */
    public function changeLang($locale)
    {
        $session = new \TetaFramework\Http\Session();
        $session->set("lang", $locale); // Guarda el nuevo locale en la sesión
    }

    /**
     * Obtiene el locale actual.
     *
     * @return string El locale actual
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Establece el locale desde la sesión, si está disponible.
     */
    public function fromSession()
    {
        $session = new \TetaFramework\Http\Session();
        if ($session->has("lang")) {
            $this->setLocale($session->get("lang")); // Establece el locale desde la sesión
        }
    }

    /**
     * Carga las traducciones desde un archivo JSON basado en el locale actual.
     */
    protected function loadTranslations()
    {
        $translationFile = __DIR__ . "/Translations/$this->locale.json"; // Ruta del archivo de traducciones
        if (file_exists($translationFile)) {
            $this->translations = json_decode(file_get_contents($translationFile), true); // Carga y decodifica las traducciones desde el archivo JSON
        }
    }

    /**
     * Traduce una clave dada al idioma actual.
     *
     * @param string $key La clave de la traducción
     * @return string La traducción correspondiente o la clave si no se encuentra
     */
    public function translate($key)
    {
        return isset($this->translations[$key]) ? $this->translations[$key] : $key; // Devuelve la traducción si existe, de lo contrario, la clave original
    }

    /**
     * Obtiene todas las traducciones cargadas.
     *
     * @return array Las traducciones actuales
     */
    public function getAllTranslate()
    {
        return $this->translations;
    }
}
