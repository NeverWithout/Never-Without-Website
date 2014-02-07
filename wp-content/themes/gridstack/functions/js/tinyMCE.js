(function( $ ){
	tinyMCE.init({
    mode : "textareas",
    theme_advanced_disable : "image"
});
	$(window).load(function(){ console.log(jQuery('.alignparallax')); });
	console.log(jQuery('.alignparallax'));
	jQuery('img.alignparallax').click(function(){
		console.log(jQuery('.mceTemp .wp_editimgbtn'));
		jQuery('.mceTemp .wp_editimgbtn').css('display', 'none');
	})
})(jQuery);