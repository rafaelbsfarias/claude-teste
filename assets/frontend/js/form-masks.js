/*
 * form-masks.js
 *
 * Máscaras e formatação de campos:
 *  - Máscara monetária (R$ 1.234,56)
 *  - Máscara dinâmica de CPF/CNPJ
 */
(function() {
    'use strict';
  
    /**
     * Formata um valor como moeda (R$ 1.234,56)
     * @param {string|number} value
     * @returns {string}
     */
    function formatCurrency(value) {
      const num = parseFloat(value);
      if (isNaN(num)) {
        return '';
      }
      // duas casas com vírgula
      let formatted = num.toFixed(2).replace('.', ',');
      // separador de milhares
      return formatted.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
  
    /**
     * Formata um CPF ou CNPJ a partir de string de dígitos
     * @param {string} value - somente dígitos
     * @returns {string} formatado
     */
    function formatDocument(value) {
      // Remove caracteres não numéricos
      let v = value.replace(/\D/g, '');
      
      // Limita o tamanho máximo para 14 dígitos (CNPJ)
      v = v.substring(0, 14);
      
      if (v.length <= 11) {
        // CPF: 000.000.000-00
        // Aplica a formatação em cada etapa da digitação
        if (v.length > 9) {
          v = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{1,2})$/, '$1.$2.$3-$4');
        } else if (v.length > 6) {
          v = v.replace(/^(\d{3})(\d{3})(\d{1,3})$/, '$1.$2.$3');
        } else if (v.length > 3) {
          v = v.replace(/^(\d{3})(\d{1,3})$/, '$1.$2');
        }
      } else {
        // CNPJ: 00.000.000/0000-00
        // Aplica a formatação em cada etapa da digitação
        if (v.length > 12) {
          v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{1,2})$/, '$1.$2.$3/$4-$5');
        } else if (v.length > 8) {
          v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{1,4})$/, '$1.$2.$3/$4');
        } else if (v.length > 5) {
          v = v.replace(/^(\d{2})(\d{3})(\d{1,3})$/, '$1.$2.$3');
        } else if (v.length > 2) {
          v = v.replace(/^(\d{2})(\d{1,3})$/, '$1.$2');
        }
      }
      
      return v;
    }
  
    /**
     * Aplica máscara monetária aos campos de valor de doação
     */
    function applyMoneyMask() {
      const fields = document.querySelectorAll('input[name="donation_value"]');
      fields.forEach(field => {
        field.addEventListener('input', () => {
          const start = field.selectionStart;
          const oldLen = field.value.length;
          let v = field.value.replace(/\D/g, '');
          if (!v) {
            field.value = '';
            return;
          }
          v = (parseInt(v, 10) / 100).toFixed(2);
          field.value = formatCurrency(v);
          const newLen = field.value.length;
          field.setSelectionRange(start + (newLen - oldLen), start + (newLen - oldLen));
        });
        field.addEventListener('blur', () => {
          let v = field.value.replace(/\D/g, '');
          field.value = v ? formatCurrency((parseInt(v, 10) / 100).toFixed(2)) : '';
        });
      });
    }
  
    /**
     * Inicializa máscaras de CPF/CNPJ via delegação de eventos
     */
    function initDocumentMasking() {
      const selector = 'input[name="cpf_cnpj"], input.cpf-cnpj, input#cpf-cnpj';
      // configura teclado numérico em mobile
      document.addEventListener('focus', function(e) {
        if (!e.target.matches(selector)) return;
        e.target.setAttribute('inputmode', 'numeric');
        e.target.setAttribute('pattern', '([0-9]{3}\.?[0-9]{3}\.?[0-9]{3}\-?[0-9]{2})|([0-9]{2}\.?[0-9]{3}\.?[0-9]{3}\/?[0-9]{4}\-?[0-9]{2})');
      }, true);
      // bloqueio de teclas não numéricas
      document.addEventListener('keydown', function(e) {
        if (!e.target.matches(selector)) return;
        const allowed = ['Backspace','Delete','ArrowLeft','ArrowRight','Tab'];
        if (!/\d/.test(e.key) && !allowed.includes(e.key)) {
          e.preventDefault();
        }
      });
      // aplica formatação no input (digitação ou colagem)
      document.addEventListener('input', function(e) {
        if (!e.target.matches(selector)) return;
        e.target.value = formatDocument(e.target.value);
      });
      
      // Adiciona evento para remover formatação antes do envio do formulário
      document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('single-donation-form') || 
            e.target.classList.contains('recurring-donation-form')) {
          
          const cpfCnpjField = e.target.querySelector(selector);
          if (cpfCnpjField) {
            // Armazena apenas os números antes do envio
            cpfCnpjField.value = cpfCnpjField.value.replace(/\D/g, '');
          }
        }
      });
    }
  
    // inicia as máscaras após DOM carregar
    document.addEventListener('DOMContentLoaded', function() {
      applyMoneyMask();
      initDocumentMasking();
    });
  
    // expõe publicamente
    window.AsaasFormMasks = {
      applyMoneyMask,
      formatCurrency,
      initDocumentMasking,
      formatDocument
    };
  })();
