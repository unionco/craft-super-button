/**
 * Super Button plugin for Craft CMS
 *
 * SuperButton Field JS
 *
 * @author    Union
 * @copyright Copyright (c) 2019 Union
 * @link      https://github.com/unionco
 * @package   SuperButton
 * @since     1.0.0SuperButtonSuperButton
 */
(function ($) {
    Craft.SuperButtonConfigurator = Garnish.Base.extend({

        $container: null,
        $types: null,
        $fields: null,
        $entrySources: null,
        $entryTypes: null,
        switches: null,

        init: function (inputNamePrefix) {
            this.inputNamePrefix = inputNamePrefix;
            this.$container = $('#' + this.inputNamePrefix);

            // types
            this.$types = this.$container.find('[data-link-type]');
            this.$fields = this.$container.find('[data-link-type-settings]');
            this.$switches = this.$container.find('.lightswitch');
            this.$entrySources = this.$container.find('[data-sources="Entry"] input[type="checkbox"]');
            this.$entryTypes = this.$container.find('[data-source-types]');

            // listeners
            this.addListener(this.$types, 'click', 'select');
            this.addListener(this.$switches, 'change', 'toggles');

            for (var index = 0; index < this.$entrySources.length; index++) {
                const element = this.$entrySources[index];
                this.addListener(element, 'change', 'entryTypes');
            }
        },

        select: function(ev) {
            var type = $(ev.target).attr('data-link-type');

            var sel = this.$container.find('[data-link-type].sel');
            var current = this.$container.find('[data-link-type-settings]:not(.hidden)');

            sel.removeClass('sel');
            current.addClass('hidden');

            $(ev.target).addClass('sel');
            this.$container.find('[data-link-type-settings="'+ type +'"]').removeClass('hidden');
        },

        toggles: function(ev) {
            var $toggle = $(ev.target);
            var $parent = $toggle.closest('[data-link-type-settings]');
            var type = $parent.attr('data-link-type-settings');

            if ($toggle.hasClass('on')) {
                this.$container.find('[data-link-type="' + type + '"]').addClass('active');
            } else {
                this.$container.find('[data-link-type="' + type + '"]').removeClass('active');
            }
        },

        entryTypes: function(ev) {
            var checkbox = $(ev.currentTarget);
            var value = checkbox.val();

            if (value !== 'singles') {
                if (value === '*') {
                    console.log('checked', checkbox.prop('checked'))
                    if (checkbox.prop('checked')) {
                        this.$container.find('[data-source-types]').each(function() {
                            $(this).removeClass('hidden');
                        })
                    } else {
                        this.$container.find('[data-source-types]').each(function () {
                            $(this).addClass('hidden');
                        })
                    }
                } else {
                    console.log('toggle on off the entry type', value);
                    var entryTypeDiv = this.$container.find('[data-source-types="'+value+'"]');
                    if (entryTypeDiv.hasClass('hidden')) {
                        entryTypeDiv.removeClass('hidden');
                    } else {
                        entryTypeDiv.addClass('hidden');
                    }
                }
            }
        }
    })
})(jQuery);
