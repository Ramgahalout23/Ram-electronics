
window.addEvent("domready",function(){


        ;(function() {
                //SqueezeBox.initialize();
                SqueezeBox.assign('.model', { parse: 'rel' });
        }).delay(1000);

        $('sliders-options').getNext().addClass('jhm-slider-option-container');

        $addon = $('jform_params_jhm_style').getElements('option:selected')[0].get('text');

        $('animation-options').getElements('a span')[0].set('text',$addon+' settings');

        $('jform_params_jhm_style').addEvent('change', function(e){

                $('animation-options').getElements('a span')[0].set('text',this.getElements('option:selected')[0].get('text')+' settings');

                $here = $('animation-options').getNext().getElements('.adminformlist li')[0];

                $ver = eval('anim'+this.get('value'));

                var $item = new Element('span',
                    {
                        'html': $ver
                        //    'html': $ver.replace(/\b(item|image)-([0-9]+)\b/g,'$1-new-0')
                });

                $('jhm-animation-setting-panel').destroy();
                $item.getChildren().inject( $here );
                $$('.hasTip').each(function(el) {
                        var title = el.get('title');

                        if (title) {
                            var parts = title.split('::', 2);
                            el.store('tip:title', parts[0]);
                            el.store('tip:text', parts[1]);
                        }
                });
                var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});


        });


        $('module-form').addEvent('submit', function(){
                $('jhm-slider-element').destroy();
        });



        $('jhm-slide-add').addEvent('click', function(){

                $html='';
                $html = $('jhm-slider-element').get('html');
                var formObjects=$('jhm-slider-element').toQueryString().parseQueryString();
                $title = $('jhm-slider-element').getElement('[ref="title"]').get('value');
                $state = $('jhm-slider-element').getElement('select.jhm-state').get('value');
                $sourcename = $('jhm-slider-element').getElement('select.jhm-sliders-source-type option:selected').get('text');

                if( $title.trim()=='' )
                    {
                    return;
                }

                $phtml = $html.replace(/\b(item|image)-([0-9]+)\b/g,'$1-added-'+jhm_item_increment);
                jhm_item_increment+=1;
                $ghtml = '<li><div class="jhm-flt-left"><span class="jhm-move" title="Move"></span></div><span class="jhm-title jhm-toggler">'+ $title+'&nbsp;&nbsp;::&nbsp;&nbsp;<span class="jhm-title-source">'+$sourcename+'</span></span><div class="jhm-flt-left"><span class="jhm-edit" title="Edit"></span>    <span ref="state" class="jhm-'+$state+'" title="published/unpublished"></span>   <span class="jhm-delete" title="Delete"></span> </div><div style="clear:both"></div> <div class="jhm-toggle-element" >';
                $ghtml += '<ul class="jhm-element">'+$phtml+'</ul></div></li>';

                var $item = new Element('span',
                    {
                        'html': $ghtml
                });

                $item.getElements('input, select, textarea').each(function(ele, i){
                        ele.set('value',  formObjects[ele.get('name')].trim() );
                });

                $item.getChildren().inject( $('sliders-slide-list') );
                $value = $('sliders-slide-list').getLast().getElements('select')[0].get('value');
                $selectInfo = $('sliders-slide-list').getLast().getElements('select')[0];
                $lastli = $('sliders-slide-list').getLast().getElement('li.jhm-sliders-source-type-li').setStyles({
                        'display':'none'
                }); 


                var $source = new Element('input', {
                        'type': 'hidden',
                        'value':$value,
                        'name':$selectInfo.get('name'),
                        'readonly':'readonly'
                });

                $source.replaces($selectInfo);

                spSortable('sliders-slide-list');
                spAccordion('.jhm-toggler-main','.jhm-toggle-element-main');
                spAccordion('.jhm-toggler','.jhm-toggle-element' );
                spAccordion('.jhm-edit','.jhm-toggle-element' );
                $$('.hasTip').each(function(el) {

                        var title = el.get('data-title');
                        if (title) {
                            var parts = title.split('::', 2);
                            el.store('tip:title', parts[0]);
                            el.store('tip:text', parts[1]);
                        }
                });
                var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});

                ;(function() {
                        //SqueezeBox.initialize();
                        SqueezeBox.assign('a.model', { parse: 'rel' });
                }).delay(1000);

                $$('.hasTip').each(function(el) {
                        var title = el.get('title');

                        if (title) {
                            var parts = title.split('::', 2);
                            el.store('tip:title', parts[0]);
                            el.store('tip:text', parts[1]);
                        }
                });

                new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});
        });

        $(document.body).addEvent("click:relay(.jhm-delete)", function(event, element) {

                conf = confirm('Are you sure to delete "'+element.getParent('li').getElements('.jhm-title')[0].get('text').trim()+'"?');
                if( conf )
                    {
                    element.getParent('li').destroy();
                } else {
                }

        });

        $(document.body).addEvent("change:relay(.jhm-toggle-element select.jhm-state)", function(event, element)
            {

                var e = this.getParent().getParent().getParent().getParent();

                e.getElements('span[ref="state"]')[0].set('class','jhm-'+ this.get('value') ); 

        });

        $(document.body).addEvent("click:relay(span[ref='state'])", function(event, element)
            {

                var e = this.getParent()
                .getParent()
                .getElements('.jhm-toggle-element select.jhm-state')[0];



                if( e.get('value')=='published' )
                    {
                    e.set('value', 'unpublished');
                    this.set('class', 'jhm-unpublished');

                } else {

                    e.set('value', 'published');
                    this.set('class', 'jhm-published');

                }

        });


        $(document.body).addEvent("blur:relay(.jhm-toggle-element input[ref='title'])", function(event, element){
                this.getParent().getParent().getParent().getParent().getChildren('span.jhm-title')[0].set('html',this.get('value').trim());
        });



        $(document.body).addEvent("hover:relay(#jhm-slider-element a.model)", function(event, element){

        });


        $(document.body).addEvent("change:relay(.jhm-toggle-element-main select.jhm-sliders-source-type)", function(event, element){

                this.getParent().getAllNext().destroy();
                $ver = eval(this.get('value')+'HTML');
                var $item = new Element('span',
                    {
                        'html': $ver
                        //    'html': $ver.replace(/\b(item|image)-([0-9]+)\b/g,'$1-new-0')
                });

                $item.getChildren().injectAfter( this.getParent() );

                ;(function() {
                        //SqueezeBox.initialize();
                        SqueezeBox.assign('a.model', { parse: 'rel' });
                }).delay(500);

                $$('.hasTip').each(function(el) {
                        var title = el.get('title');

                        if (title) {
                            var parts = title.split('::', 2);
                            el.store('tip:title', parts[0]);
                            el.store('tip:text', parts[1]);
                        }
                });
                new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});
        });

        function spSortable(id)
        {
            new Sortables($(id), {
                    clone:true,
                    revert: true,
                    constrain: true,
                    opacity: 0.7,
                    handle : 'span.jhm-move',
                    initialize: function() {},
                    onComplete: function(el){}
            });
        }

        function spAccordion(handler, element, displays) {
            if( typeof(displays)==='undefined' ){
                displays = -1;
            }
            new Fx.Accordion($$(handler), $$(element),{
                    alwaysHide: true,
                    display: displays,
                    onActive: function(toggler) { toggler.getParent().addClass('jhm-toggler-open'); },
                    onBackground: function(toggler) { toggler.getParent().removeClass('jhm-toggler-open'); }
            })
        }

        spSortable('sliders-slide-list');
        spAccordion('.jhm-toggler-main','.jhm-toggle-element-main');
        spAccordion('.jhm-toggler','.jhm-toggle-element' );
        spAccordion('.jhm-edit','.jhm-toggle-element' );
});