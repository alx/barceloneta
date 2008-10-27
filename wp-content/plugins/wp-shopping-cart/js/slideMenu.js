// this needs converting to jquery, unfortunately some fuckwit has been DDOSing the jquery site
jQuery(document).ready(
  function()
  {
	jQuery('h3.category').each(function(div){
		var block = div.getNext();
		
		var fx = new Fx.Slide(block).hide();

		div.addEvent('click', function(){
			fx.toggle();
		});
		
	});
		
});