"use strict";

;
(function ($, window, document, undefined) {
  'use strict';

  var MerchantSave = MerchantSave || {};
  $(document).ready(function () {
    $('.merchant-module-page-setting-field').each(function () {
      var $field = $(this);
      if ($field.data('condition') && $field.data('condition').length) {
        var condition = $field.data('condition');
        var $target = $('input[name="merchant[' + condition[0] + ']"]');
        if ($target.length) {
          $target.on('change merchant.change', function (e) {
            var $element = $(this);
            var passed = false;
            switch (condition[1]) {
              case '==':
                if ($element.attr('type') === 'radio' || $element.attr('type') === 'checkbox') {
                  if ($element.is(':checked') && $element.val() == condition[2]) {
                    passed = true;
                  }
                }
                break;
            }
            if (passed) {
              $field.removeClass('merchant-hide').addClass('merchant-show');
            } else {
              $field.removeClass('merchant-show').addClass('merchant-hide');
            }
          }).trigger('merchant.change');
        }
      }
    });

    // AjaxSave
    var $ajaxForm = $('.merchant-module-page-ajax-form');
    var $ajaxHeader = $('.merchant-module-page-ajax-header');
    var $ajaxSaveBtn = $('.merchant-module-save-button');
    $('.merchant-module-page-content').on('change keypress change.merchant', ':input:not(.merchant-module-question-answer-textarea)', function () {
      if (!MerchantSave.show_save) {
        $ajaxHeader.addClass('merchant-show');
        $ajaxHeader.removeClass('merchant-saving');
        MerchantSave.show_save = true;
      }
    });
    $ajaxForm.ajaxForm({
      beforeSubmit: function beforeSubmit() {
        $ajaxHeader.addClass('merchant-saving');
      },
      success: function success() {
        $ajaxHeader.removeClass('merchant-show');
        MerchantSave.show_save = false;
      }
    });
    $('.merchant-module-question-answer-button').on('click', function (e) {
      e.preventDefault();
      var $button = $(this);
      var $textarea = $('.merchant-module-question-answer-textarea');
      $('.merchant-module-question-answer-dropdown').removeClass('merchant-show');
      $('.merchant-module-question-thank-you-dropdown').addClass('merchant-show');
      window.wp.ajax.post('merchant_module_feedback', {
        subject: $textarea.data('subject'),
        message: $textarea.val(),
        nonce: window.merchant.nonce
      });
    });
    $('.merchant-module-page-button-action-activate').on('click', function (e) {
      e.preventDefault();
      $('.merchant-module-question-list-dropdown').removeClass('merchant-show');
      $('.merchant-module-question-answer-dropdown').removeClass('merchant-show');
      $('.merchant-module-question-answer-form').removeClass('merchant-show');
      $('.merchant-module-question-answer-title').removeClass('merchant-show');
      $('.merchant-module-question-thank-you-dropdown').removeClass('merchant-show');
      $('.merchant-module-question-answer-textarea').val('');
      window.wp.ajax.post('merchant_module_activate', {
        module: $(this).data('module'),
        nonce: window.merchant.nonce
      }).done(function () {
        $('.merchant-module-action').addClass('merchant-enabled');
      });
    });
    $('.merchant-module-page-button-action-deactivate').on('click', function (e) {
      e.preventDefault();
      window.wp.ajax.post('merchant_module_deactivate', {
        module: $(this).data('module'),
        nonce: window.merchant.nonce
      }).done(function () {
        $('.merchant-module-action').removeClass('merchant-enabled');
        $('.merchant-module-question-list-dropdown').addClass('merchant-show');
      });
    });
    $('.merchant-module-question-list-dropdown li').on('click', function (e) {
      var $question = $(this);
      var index = $question.index();
      var target = $question.data('answer-target');
      var $answer = $('[data-answer-title="' + target + '"]');
      if ($answer.length) {
        $answer.addClass('merchant-show').siblings().removeClass('merchant-show');
        $('.merchant-module-question-answer-dropdown').addClass('merchant-show');
        $('.merchant-module-question-answer-textarea').attr('data-subject', target);
      } else {
        $('.merchant-module-question-thank-you-dropdown').addClass('merchant-show');
        $('.merchant-module-question-answer-dropdown').removeClass('merchant-show');
      }
      $('.merchant-module-question-answer-textarea').val('');
      $('.merchant-module-question-list-dropdown').removeClass('merchant-show');
    });
    $('.merchant-module-dropdown-close').on('click', function (e) {
      e.preventDefault();
      $(this).closest('.merchant-module-dropdown').removeClass('merchant-show');
    });
    $('.merchant-module-page-button-deactivate').on('click', function (e) {
      e.preventDefault();
      var $button = $(this);
      var $dropdown = $('.merchant-module-deactivate-dropdown');
      $dropdown.toggleClass('merchant-show');
      $(document).off('click.merchant-close');
      $(document).on('click.merchant-close', function (e) {
        if (!$(e.target).closest('.merchant-module-deactivate').length) {
          $dropdown.removeClass('merchant-show');
          $(document).off('click.merchant-close');
        }
      });
    });
    $('.merchant-range').each(function () {
      var $range = $(this);
      var $rangeInput = $range.find('.merchant-range-input');
      var $numberInput = $range.find('.merchant-range-number-input');
      $rangeInput.on('change input merchant.range merchant-init.range', function (e) {
        var $range = $(this);
        var value = (e.type === 'merchant' ? $numberInput.val() : $range.val()) || 0;
        var min = $range.attr('min') || 0;
        var max = $range.attr('max') || 1;
        var percentage = (value - min) / (max - min) * 100;
        $range.css({
          'background': 'linear-gradient(to right, #3858E9 0%, #3858E9 ' + percentage + '%, #ddd ' + percentage + '%, #ddd 100%)'
        });
        $rangeInput.val(value);
        $numberInput.val(value);
      }).trigger('merchant-init.range');
      $numberInput.on('change input blur', function () {
        if ($rangeInput.hasClass('merchant-range-input')) {
          $rangeInput.val($(this).val()).trigger('merchant.range');
        }
      });
    });
    $('.merchant-color').each(function () {
      var $color = $(this);
      var $picker = $color.find('.merchant-color-picker');
      var $input = $color.find('.merchant-color-input');
      var inited;
      var pickr;
      $picker.on('click', function () {
        var $bodyHTML = $('body,html');
        $bodyHTML.addClass('merchant-height-auto');
        if (!inited) {
          pickr = new Pickr({
            el: $picker.get(0),
            container: 'body',
            theme: 'merchant',
            appClass: 'merchant-pcr-app',
            default: $input.val() || '#212121',
            swatches: ['#000000', '#F44336', '#E91E63', '#673AB7', '#03A9F4', '#8BC34A', '#FFEB3B', '#FFC107', '#FFFFFF'],
            sliders: 'h',
            useAsButton: true,
            components: {
              hue: true,
              preview: true,
              opacity: true,
              interaction: {
                input: true,
                clear: true
              }
            },
            i18n: {
              'btn:clear': 'Default'
            }
          });
          pickr.on('change', function (color) {
            var colorCode;
            if (color.a === 1) {
              pickr.setColorRepresentation('HEX');
              colorCode = color.toHEXA().toString(0);
            } else {
              pickr.setColorRepresentation('RGBA');
              colorCode = color.toRGBA().toString(0);
            }
            $picker.css({
              'background-color': colorCode
            });
            if ($input.val() !== colorCode) {
              $input.val(colorCode).trigger('change.merchant');
            }
          });
          pickr.on('clear', function () {
            var defaultColor = $picker.data('default-color');
            if (defaultColor) {
              pickr.setColor(defaultColor);
            } else {
              $picker.css({
                'background-color': 'white'
              });
              $input.val('');
            }
          });
          pickr.on('hide', function () {
            $bodyHTML.removeClass('merchant-height-auto');
          });
          $picker.data('pickr', pickr);
          setTimeout(function () {
            pickr.show();
          });
          inited = true;
        } else {
          pickr.setColor($input.val());
        }
      });
      $input.on('change keyup', function () {
        var colorCode = $(this).val();
        $picker.css({
          'background-color': colorCode
        });
      });
    });
    $('.merchant-module-page-setting-field-gallery').each(function () {
      var $this = $(this);
      var $button = $this.find('.merchant-gallery-button');
      var $input = $this.find('.merchant-gallery-input');
      var $images = $this.find('.merchant-gallery-images');
      var $remove = $this.find('.merchant-gallery-remove');
      var wpMediaFrame;
      var sortable = $images.sortable({
        helper: 'original',
        update: function update(event, ui) {
          var selectedIds = [];
          $images.find('.merchant-gallery-image').each(function () {
            selectedIds.push($(this).data('item-id'));
          });
          $input.val(selectedIds.join(',')).trigger('change');
        }
      });
      $remove.on('click', function (e) {
        e.preventDefault();
        $(this).parent().remove();
        var selectedIds = [];
        $images.find('.merchant-gallery-image').each(function () {
          selectedIds.push($(this).data('item-id'));
        });
        $input.val(selectedIds.join(',')).trigger('change');
      });
      $button.on('click', function (e) {
        var $btn = $(this);
        var ids = $input.val();
        var mode = ids ? 'edit' : 'add';
        e.preventDefault();
        if (typeof window.wp === 'undefined' || !window.wp.media || !window.wp.media.gallery) {
          return;
        }
        if (mode === 'add') {
          wpMediaFrame = window.wp.media({
            library: {
              type: 'image'
            },
            frame: 'post',
            state: 'gallery',
            multiple: true
          });
          wpMediaFrame.open();
        } else {
          wpMediaFrame = window.wp.media.gallery.edit('[gallery ids="' + ids + '"]');
        }
        wpMediaFrame.on('update', function (selection) {
          $images.empty();
          var selectedIds = selection.models.map(function (attachment) {
            var item = attachment.toJSON();
            var thumb = item.sizes && item.sizes.thumbnail && item.sizes.thumbnail.url ? item.sizes.thumbnail.url : item.url;
            $images.append('<div class="merchant-gallery-image" data-item-id="' + item.id + '"><i class="merchant-gallery-remove dashicons dashicons-no-alt"></i><img src="' + thumb + '" /></div>');
            return item.id;
          });
          $input.val(selectedIds.join(',')).trigger('change');
          $this.find('.merchant-gallery-remove').on('click', function (e) {
            e.preventDefault();
            $(this).parent().remove();
            var selectedIds = [];
            $images.find('.merchant-gallery-image').each(function () {
              selectedIds.push($(this).data('item-id'));
            });
            $input.val(selectedIds.join(',')).trigger('change');
          });
        });
      });
    });
    $('.merchant-module-page-setting-field-upload').each(function () {
      var $this = $(this);
      var $button = $this.find('.merchant-upload-button');
      var $input = $this.find('.merchant-upload-input');
      var $wrapper = $this.find('.merchant-upload-wrapper');
      var $remove = $this.find('.merchant-upload-remove');
      var wpMediaFrame;
      $remove.on('click', function (e) {
        e.preventDefault();
        $(this).parent().remove();
        $input.val('').trigger('change');
      });
      $button.on('click', function (e) {
        e.preventDefault();
        if (typeof window.wp === 'undefined' || !window.wp.media) {
          return;
        }
        if (!wpMediaFrame) {
          wpMediaFrame = window.wp.media({
            library: {
              type: 'image'
            }
          });
        }
        wpMediaFrame.open();
        wpMediaFrame.on('select', function () {
          $wrapper.empty();
          var item = wpMediaFrame.state().get('selection').first().attributes;
          var thumb = item.sizes && item.sizes.thumbnail && item.sizes.thumbnail.url ? item.sizes.thumbnail.url : item.url;
          $wrapper.append('<div class="merchant-upload-image"><i class="merchant-upload-remove dashicons dashicons-no-alt"></i><img src="' + thumb + '" /></div>');
          $input.val(item.id).trigger('change');
          $this.find('.merchant-upload-remove').on('click', function (e) {
            e.preventDefault();
            $(this).parent().remove();
            $input.val('').trigger('change');
          });
        });
      });
    });
  });
})(jQuery, window, document);