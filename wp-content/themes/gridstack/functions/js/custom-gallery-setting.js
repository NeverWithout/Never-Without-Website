/**
 * Custom Gallery Setting
 */
( function( $ ) {

    var media = wp.media;

    // Wrap the render() function to append controls
    media.view.Settings.Gallery = media.view.Settings.Gallery.extend({
        render: function() {
            media.view.Settings.prototype.render.apply( this, arguments );

            // Remove original Linkto
            this.$el.find('select.link-to').closest('label').remove();

			// Change the orderby-box order
            orderByBox  = this.$el.find('[data-setting*="_orderbyRandom"]').closest('label').remove();

            // Remove Columns dialog
            this.$el.find('select.columns').val('3');
            this.model.set('columns', '3');
            this.$el.find('select.columns').closest('label').remove();

            // Append the custom templates in our order
            this.$el.append( media.template( 'custom-gallery-type' ) );
            this.$el.append( orderByBox );
            this.$el.append( media.template( 'custom-gallery-width' ) );
            this.$el.append( media.template( 'custom-gallery-cropping' ) );
            this.$el.append( media.template( 'custom-gallery-link' ) );
			
			// Load gallery the settings
            media.gallery.defaults.type = 'gallery';
            this.update.apply( this, ['type'] );

			// Load the crop settings
            media.gallery.defaults.crop = 'crop';
            this.update.apply( this, ['crop'] );

			// Load the width settings
            media.gallery.defaults.width = 'fixed';
            this.update.apply( this, ['width'] );

			// Load the link settings
            media.gallery.defaults.link = 'full';
            this.update.apply( this, ['link'] );
			
			// Set the variables used in switchers
			var gallerySettings = this.$el.closest('.gallery-settings'),
				galleryType 	= this.$el.find('select.gallery-type').val(),
				widthSettings	= gallerySettings.find('.gallery-width'),
                hideSettings 	= gallerySettings.find('.gallery-crop'),
                originalCrop 	= hideSettings.val();
			
			// Show default boxes 
			switch(galleryType) {
				case 'carousel':
					hideSettings.hide();
					hideSettings.val('crop');
					break;
				case 'gallery':
						hideSettings.add(widthSettings).hide();
						hideSettings.val('crop');
						widthSettings.val('fixed');
					break;
				default:
					hideSettings.add(widthSettings).show();
					hideSettings.val(originalCrop);
					break;
			}	

            // Fade effect for different options
            this.$el.find('select.gallery-type').change(function() {
                var $valueGal 		= $(this).val();

                    switch($valueGal) {
                        case 'carousel':
							widthSettings.fadeIn();
                            hideSettings.fadeOut(500, function(){
                                hideSettings.val('crop');
                            });
                            break;
						case 'gallery':
						    hideSettings.add(widthSettings).fadeOut(500, function(){
                                hideSettings.val('crop');
								widthSettings.val('fixed');
                            });
							break;
                        default:
                            hideSettings.add(widthSettings).fadeIn(500);
                            hideSettings.val(originalCrop);
                            break;

                    }
            });

            return this;
        }
    } );

    var _AttachmentDisplay = wp.media.view.Settings.AttachmentDisplay;
    wp.media.view.Settings.AttachmentDisplay = _AttachmentDisplay.extend({
        className: 'attachment-display-settings',
        render: function() {
            _AttachmentDisplay.prototype.render.apply(this, arguments);

            /** 
             * Function to double check image width
             * @param  item         Item to check
             * @param  selector     Size selector
             * @param  originalthis Original this
             * @return Error message
             */
            function themewichCheckWidth(item, selector, originalthis, warning) {
                if (item.length !== 0) {
                    item.removeAttr('disabled');
                } else {
                  warning.fadeIn(); // Fade in waring
                  selector.closest('.setting').fadeOut();
                  originalthis.model.set('size', 'full');
                }
            }

			// Add additional alignments
            this.$el.find('select.alignment option[value="left"]').before('<option value="fixed">Extended</option><option value="parallax">Parallax</option>');			
			
			 // Add error message for use
            this.$el.append('<div class="clear"></div><div class="error notwide" style="display:none"><p>Your original image is not large enough for this layout option. It should be at least 1500px wide. Substitute size is being used.</p></div>');

            // Get varibles
            var originalthis 	= this,
				$originalthis 	= $(originalthis),
				$parentcontain 	= this.$el.closest('.attachment-display-settings')
			    $selector       = $parentcontain.find('select.size'),
				$options        = $parentcontain.find('select.size option'),
				$warning        = $parentcontain.find('.notwide'),
				$linkToSetting  = $parentcontain.find('.link-to').closest('.setting'),
				fullCropped     = $parentcontain.find('select.size option[value=fullslideshow]'),
				fullUnCropped   = $parentcontain.find('select.size option[value=fullslideshownc]'),
				fullFixed       = $parentcontain.find('select.size option[value=fixedslideshow]'),
				fullFixednc     = $parentcontain.find('select.size option[value=fixedslideshownc]');
			
			// Remove post as an option
            this.$el.find('select.link-to option[value=post]').remove();
            this.updateLinkTo();

            /**
             * Fade and disable options
             */
            this.$el.find('select.alignment').change(function() {

                var $valueGal = $(this).val();
				

                    switch($valueGal) {

                        /**
                         * Parallax Option
                         */
                        case 'parallax':

                            // Disable options and sizes
                            $options.attr('disabled','disabled');
							fullFixednc.removeAttr('disabled');
							
							// Show/Hide Settings
                            $linkToSetting.fadeIn(500);
							$selector.closest('.setting').fadeIn(500); 
                            $warning.fadeOut(500);

                            // Check to see if wide enough
                            themewichCheckWidth(fullUnCropped, $selector, originalthis, $warning);

                            // Select correct size and set it
                            $selector.val('fullslideshownc');
                            originalthis.model.set('size', 'fullslideshownc');

                            break;

                        /**
                         * Fixed Width Option
                         */
                        case 'fixed':

                            // Disable options and sizes
                            $options.attr('disabled','disabled');
                            fullFixed.removeAttr('disabled');
                            fullFixednc.removeAttr('disabled');
							
							// Show/Hide Settings
                            $linkToSetting.fadeIn(500);
							$selector.closest('.setting').fadeIn(500);
                            $warning.fadeOut(500);

                            // Select correct size and set it
                            $selector.val('fixedslideshow');
                            originalthis.model.set('size', 'fixedslideshow');

                            break;

                        /**
                         * Regular Options
                         */
                        case 'left':
                        case 'right':
                        case 'center':
                        case 'none':

                            // Disable options and sizes
                            $options.removeAttr('disabled');
                            fullCropped
                                .add(fullUnCropped)
                                .add(fullFixed)
                                .add(fullFixednc)
                                .attr('disabled','disabled');
								
							// Show/Hide Settings	
                            $linkToSetting.fadeIn(500);
                            $warning.fadeOut(500);
							$selector.closest('.setting').fadeIn(500);  

                            // Select correct size and set it
                            $selector.val('large');
                            originalthis.model.set('size', 'large');
                            
                            break;

                        /**
                         * Default just in case
                         */
                        default:
                            
                            // Disable options and sizes
                            $options.removeAttr('disabled');
							// Show/Hide Settings
                            $linkToSetting.fadeIn(500);
                            $warning.fadeOut(500);
							$selector.closest('.setting').fadeIn(500); 

                            break;

                    }
            });

        }
    });

} )( jQuery );