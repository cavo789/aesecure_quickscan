jQuery( document ).ready( function( $ ) {
    var switchLock = function (key, obj) {
        $(obj).parent().parent().find('.tzportfolio-lock').find('span').removeClass('active');
        $(obj).parent().parent().find('.tzportfolio-lock').find(key).addClass('active');
    };
    var switchContent = function (key, obj) {
        $(obj).parent().parent().parent().find('.tzportfolio-box').removeClass('active');
        $(obj).parent().parent().parent().find('.tzportfolio-tabcontent-container').find(key).addClass('active');
    };

    var tzPortfolioPlusAddonAdminFieldAction = function(container){
        container   = typeof container !== "undefined"?container:"body";

        $(container).on("click", ".tzportfolio-box-responsive .tzportfolio-device", function (e) {
            $(this).parent().find('.tzportfolio-device').removeClass('active');
            $(this).addClass('active');
            if ($(this).hasClass('md')) {
                switchLock('.md', this);
                switchContent('.md', this);
            }
            if ($(this).hasClass('sm')) {
                switchLock('.sm', this);
                switchContent('.sm', this);
            }
            if ($(this).hasClass('xs')) {
                switchLock('.xs', this);
                switchContent('.xs', this);
            }
        });
        $(container).on("click",".tzportfolio-box-responsive .tzportfolio-lock>span", function (e) {
            if ($(this).find('i').hasClass('icon-locked')) {
                $(this).find('i').removeClass().addClass('icon-unlock');
            } else {
                $(this).find('i').removeClass().addClass('icon-locked');
            }
        });

        $(container).on("input",".tzportfolio-box-responsive .tzportfolio-box-item>input", function (e) {
            if ($(this).parent().parent().parent().parent().find('.tzportfolio-lock').find('.active').find('i').hasClass('icon-locked')) {
                $(this).parent().parent().find('input').val($(this).val());
            }
        });


        $(container).find('.tzfont-container').each(function(i, el) {
            el = $(el);

            var base_id = el.find('input.typoData');
            // $(base_id).attr('id', $(base_id).data('id'));
            var base_el = $(base_id);
            base_id =   $(base_id).data('id');


            if(base_el.val() === '') base_el.attr('value','{"fontFamily":"","lineHeight":"","fontWeight":"","letterSpacing":"","fontSize":"","fontStyle":"","textTransform":"","textDecoration":""}');

            var values = $.parseJSON(base_el.val());

            el.find('.' + base_id + '_fontfamily').change(function() {
                values.fontFamily = el.find('.' + base_id + '_fontfamily').val();
                base_el.attr('value', JSON.stringify( values ));
            });

            el.find('.' + base_id + '_fontweight').change(function() {
                values.fontWeight = el.find('.' + base_id + '_fontweight').val();
                base_el.attr('value', JSON.stringify( values ));
            });

            el.find('.' + base_id + '_fontsize').change(function() {
                values.fontSize = el.find('.' + base_id + '_fontsize').val();
                base_el.attr('value', JSON.stringify( values ));
            });

            el.find('.' + base_id + '_lineheight').change(function() {
                values.lineHeight = el.find('.' + base_id + '_lineheight').val();
                base_el.attr('value', JSON.stringify( values ));
            });

            el.find('.' + base_id + '_fontstyle').change(function() {
                values.fontStyle = el.find('.' + base_id + '_fontstyle').val();
                base_el.attr('value', JSON.stringify( values ));
            });

            el.find('.' + base_id + '_letterspacing').change(function() {
                values.letterSpacing = el.find('.' + base_id + '_letterspacing').val();
                base_el.attr('value', JSON.stringify( values ));
            });

            el.find('.' + base_id + '_text_transform').change(function() {
                values.textTransform = el.find('.' + base_id + '_text_transform').val();
                base_el.attr('value', JSON.stringify( values ));
            });

            el.find('.' + base_id + '_text_decoration').change(function() {
                values.textDecoration = el.find('.' + base_id + '_text_decoration').val();
                base_el.attr('value', JSON.stringify( values ));
            });
        });

        $(container).find('.tzportfolio-box-responsive').each(function(i, el) {
            el = $(el);

            var base_id = el.find('input.pm-data');

            base_id =   $(base_id).attr('id');

            var base_el = $('#' + base_id);
            if(base_el.val() === '') base_el.attr('value','{"md": {"top": "", "right": "", "bottom": "", "left": ""}, "sm": {"top":"", "right":"", "bottom":"", "left":""}, "xs": {"top":"", "right":"", "bottom":"", "left":""}}');

            var values = $.parseJSON(base_el.val());
            el.find('.tzportfolio-box').each(function (i, group) {
                var $this   =   $(group);
                var tmp     =   '';
                $this.find('input').each(function (i, pmbox){
                    if (tmp === '') {
                        tmp = $(pmbox).val();
                    } else if (tmp !== $(pmbox).val()) {
                        var thisclass   =   '';
                        if ($(pmbox).parent().parent().hasClass('md')) {
                            thisclass   =   'md';
                        }
                        if ($(pmbox).parent().parent().hasClass('sm')) {
                            thisclass   =   'sm';
                        }
                        if ($(pmbox).parent().parent().hasClass('xs')) {
                            thisclass   =   'xs';
                        }
                        $(pmbox).parent().parent().parent().parent().find('.tzportfolio-lock').find('.'+thisclass).find('i').removeClass('icon-locked').addClass('icon-unlock');
                    }
                });
                $this.find('input').on('change', function (){
                    var data    =   {};
                    data.top    =   $this.find('input.pm-top').val();
                    data.right    =   $this.find('input.pm-right').val();
                    data.bottom    =   $this.find('input.pm-bottom').val();
                    data.left    =   $this.find('input.pm-left').val();
                    if ($this.hasClass('md')) {
                        values.md = data;
                    }
                    if ($this.hasClass('sm')) {
                        values.sm = data;
                    }
                    if ($this.hasClass('xs')) {
                        values.xs = data;
                    }
                    base_el.attr('value', JSON.stringify( values ));
                });
            });
        });
    };

    tzPortfolioPlusAddonAdminFieldAction();

    /*ES6+ js with joomla 4*/
    document.addEventListener('subform-row-add', ({ detail: { row } }) => {
        tzPortfolioPlusAddonAdminFieldAction(row);
    });

    $(document).on('subform-row-add', function(event, row){
        tzPortfolioPlusAddonAdminFieldAction(row);
    });
});