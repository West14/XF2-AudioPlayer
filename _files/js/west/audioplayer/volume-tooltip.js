!function (window, document) {
    'use strict'

    XF.WVolumeTooltip = XF.Element.newHandler({
        options: {
            playerId: null,
        },

        trigger: null,
        tooltip: null,
        player: null,

        init: function ()
        {
            const $playerEl = $(document.getElementById(this.options.playerId))
            this.player = XF.Element.getHandler($playerEl, 'w-audio-player')

            this.tooltip = new XF.TooltipElement(XF.proxy(this, 'getTooltip'), {
                extraClass: 'tooltip--preview tooltip--wapVolume',
                html: true,
            })

            this.trigger = new XF.TooltipTrigger(this.$target, this.tooltip, {
                maintain: true,
                trigger: 'hover touchhold',
            });

            this.tooltip.getTooltip()[0].querySelector('.wapVolume-input')
                .addEventListener('input', XF.proxy(this, 'onVolumeInput'))

            this.trigger.init();
        },

        onVolumeInput: function (e)
        {
            this.player.setVolume(e.target.value / 100)
        },

        getTooltip: function ()
        {
            return $(document.querySelector('#wapVolumeTooltipTemplate').innerHTML)
        }
    })

    XF.Element.register('wap-volume-tooltip', 'XF.WVolumeTooltip')
} (window, document)