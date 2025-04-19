<?php
/**
 * Registra todos os hooks com WordPress.
 *
 * @package Asaas_Customer_Registration
 * @since   1.0.0
 */

namespace Asaas_Customer_Registration\Includes;

/**
 * Mantém uma lista de todos os hooks a serem registrados com WordPress.
 *
 * Esta classe implementa o padrão de design Observer para facilitar o gerenciamento
 * de todos os hooks que o plugin registra com o core do WordPress.
 */
class Asaas_Loader {

    /**
     * Array de ações registradas com WordPress.
     *
     * @var array
     * @since 1.0.0
     */
    protected $actions;

    /**
     * Array de filtros registrados com WordPress.
     *
     * @var array
     * @since 1.0.0
     */
    protected $filters;

    /**
     * Inicializa as coleções usadas para manter actions e filters.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->actions = array();
        $this->filters = array();
    }

    /**
     * Adiciona uma action a ser registrada com WordPress.
     *
     * @param string   $hook          Nome do hook WordPress.
     * @param object   $component     Referência ao objeto.
     * @param string   $callback      Nome do método a ser chamado.
     * @param int      $priority      Opcional. Prioridade do hook. Default 10.
     * @param int      $accepted_args Opcional. Número de argumentos. Default 1.
     * @since 1.0.0
     */
    public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Adiciona um filtro a ser registrado com WordPress.
     *
     * @param string   $hook          Nome do hook WordPress.
     * @param object   $component     Referência ao objeto.
     * @param string   $callback      Nome do método a ser chamado.
     * @param int      $priority      Opcional. Prioridade do hook. Default 10.
     * @param int      $accepted_args Opcional. Número de argumentos. Default 1.
     * @since 1.0.0
     */
    public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
        $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Função utilitária usada para registrar actions e filters em suas respectivas coleções.
     *
     * @param array    $hooks         Coleção de hooks a ser registrada.
     * @param string   $hook          Nome do hook WordPress.
     * @param object   $component     Referência ao objeto.
     * @param string   $callback      Nome do método a ser chamado.
     * @param int      $priority      Prioridade do hook.
     * @param int      $accepted_args Número de argumentos.
     * @return array   Coleção de ações/filtros com o novo hook registrado.
     * @since 1.0.0
     */
    private function add($hooks, $hook, $component, $callback, $priority, $accepted_args) {
        $hooks[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args
        );

        return $hooks;
    }

    /**
     * Registra os filtros e ações com WordPress.
     *
     * @since 1.0.0
     */
    public function run() {
        // Registra actions
        foreach ($this->actions as $hook) {
            add_action(
                $hook['hook'],
                array($hook['component'], $hook['callback']),
                $hook['priority'],
                $hook['accepted_args']
            );
        }

        // Registra filters
        foreach ($this->filters as $hook) {
            add_filter(
                $hook['hook'],
                array($hook['component'], $hook['callback']),
                $hook['priority'],
                $hook['accepted_args']
            );
        }
    }
}