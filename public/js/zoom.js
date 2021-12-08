(function ($) {
    $(document).ready(function() {
        $('.xzoom, .xzoom-gallery').xzoom({zoomWidth: 400, title: true, tint: '#333', Xoffset: 15});
        $('.xzoom2, .xzoom-gallery2').xzoom({position: '#xzoom2-id', tint: '#ffa200'});
        $('.xzoom3, .xzoom-gallery3').xzoom({position: 'lens', lensShape: 'circle', sourceClass: 'xzoom-hidden'});
        $('.xzoom4, .xzoom-gallery4').xzoom({tint: '#006699', Xoffset: 15});
        $('.xzoom5, .xzoom-gallery5').xzoom({tint: '#006699', Xoffset: 15});

//Integration with hammer.js
        var isTouchSupported = 'ontouchstart' in window;

        if (isTouchSupported) {
//If touch device
            $('.xzoom, .xzoom2, .xzoom3, .xzoom4, .xzoom5').each(function(){
                var xzoom = $(this).data('xzoom');
                xzoom.eventunbind();
            });

            $('.xzoom, .xzoom2, .xzoom3').each(function() {
                var xzoom = $(this).data('xzoom');
                $(this).hammer().on("tap", function(event) {
                    event.pageX = event.gesture.center.pageX;
                    event.pageY = event.gesture.center.pageY;
                    var s = 1, ls;

                    xzoom.eventmove = function(element) {
                        element.hammer().on('drag', function(event) {
                            event.pageX = event.gesture.center.pageX;
                            event.pageY = event.gesture.center.pageY;
                            xzoom.movezoom(event);
                            event.gesture.preventDefault();
                        });
                    }

                    xzoom.eventleave = function(element) {
                        element.hammer().on('tap', function(event) {
                            xzoom.closezoom();
                        });
                    }
                    xzoom.openzoom(event);
                });
            });

            $('.xzoom4').each(function() {
                var xzoom = $(this).data('xzoom');
                $(this).hammer().on("tap", function(event) {
                    event.pageX = event.gesture.center.pageX;
                    event.pageY = event.gesture.center.pageY;
                    var s = 1, ls;

                    xzoom.eventmove = function(element) {
                        element.hammer().on('drag', function(event) {
                            event.pageX = event.gesture.center.pageX;
                            event.pageY = event.gesture.center.pageY;
                            xzoom.movezoom(event);
                            event.gesture.preventDefault();
                        });
                    }

                    var counter = 0;
                    xzoom.eventclick = function(element) {
                        element.hammer().on('tap', function() {
                            counter++;
                            if (counter == 1) setTimeout(openfancy,300);
                            event.gesture.preventDefault();
                        });
                    }

                    function openfancy() {
                        if (counter == 2) {
                            xzoom.closezoom();
                            $.fancybox.open(xzoom.gallery().cgallery);
                        } else {
                            xzoom.closezoom();
                        }
                        counter = 0;
                    }
                    xzoom.openzoom(event);
                });
            });

            $('.xzoom5').each(function() {
                var xzoom = $(this).data('xzoom');
                $(this).hammer().on("tap", function(event) {
                    event.pageX = event.gesture.center.pageX;
                    event.pageY = event.gesture.center.pageY;
                    var s = 1, ls;

                    xzoom.eventmove = function(element) {
                        element.hammer().on('drag', function(event) {
                            event.pageX = event.gesture.center.pageX;
                            event.pageY = event.gesture.center.pageY;
                            xzoom.movezoom(event);
                            event.gesture.preventDefault();
                        });
                    }

                    var counter = 0;
                    xzoom.eventclick = function(element) {
                        element.hammer().on('tap', function() {
                            counter++;
                            if (counter == 1) setTimeout(openmagnific,300);
                            event.gesture.preventDefault();
                        });
                    }

                    function openmagnific() {
                        if (counter == 2) {
                            xzoom.closezoom();
                            var gallery = xzoom.gallery().cgallery;
                            var i, images = new Array();
                            for (i in gallery) {
                                images[i] = {src: gallery[i]};
                            }
                            $.magnificPopup.open({items: images, type:'image', gallery: {enabled: true}});
                        } else {
                            xzoom.closezoom();
                        }
                        counter = 0;
                    }
                    xzoom.openzoom(event);
                });
            });

        } else {
//If not touch device

//Integration with fancybox plugin
            $('#xzoom-fancy').bind('click', function(event) {
                var xzoom = $(this).data('xzoom');
                xzoom.closezoom();
                $.fancybox.open(xzoom.gallery().cgallery, {padding: 0, helpers: {overlay: {locked: false}}});
                event.preventDefault();
            });

//Integration with magnific popup plugin
            $('#xzoom-magnific').bind('click', function(event) {
                var xzoom = $(this).data('xzoom');
                xzoom.closezoom();
                var gallery = xzoom.gallery().cgallery;
                var i, images = new Array();
                for (i in gallery) {
                    images[i] = {src: gallery[i]};
                }
                $.magnificPopup.open({items: images, type:'image', gallery: {enabled: true}});
                event.preventDefault();
            });
        }
    });
})(jQuery);




// LOUPE: inspired by https://www.youtube.com/watch?v=93rFFW1n7Ec

// zoom=2;
//
// document.getElementById("image").onmousemove=function () {
//     loupe=document.getElementById("loupe");
//     loupe.style.left=event.clientX+"px";
//     loupe.style.top=event.clientY+"px";
//     loupe.style.backgroundSize=(1500*zoom)+"px";
//     loupe.style.backgroundPosition=(-loupe.offsetLeft*zoom-150)+"px"+(-loupe.offsetTop*zoom-150)+"px";
// }



//ZOOM
// function imageZoom(imgID, resultID) {
//     var img, lens, result, cx, cy;
//     img = document.getElementById(imgID);
//     result = document.getElementById(resultID);
//     /* Create lens: */
//     lens = document.createElement("DIV");
//     lens.setAttribute("class", "img-zoom-lens");
//     /* Insert lens: */
//     img.parentElement.insertBefore(lens, img);
//     /* Calculate the ratio between result DIV and lens: */
//     cx = result.offsetWidth / lens.offsetWidth;
//     cy = result.offsetHeight / lens.offsetHeight;
//     /* Set background properties for the result DIV */
//     result.style.backgroundImage = "url('" + img.src + "')";
//     result.style.backgroundSize = (img.width * cx) + "px " + (img.height * cy) + "px";
//     /* Execute a function when someone moves the cursor over the image, or the lens: */
//     lens.addEventListener("mousemove", moveLens);
//     img.addEventListener("mousemove", moveLens);
//     /* And also for touch screens: */
//     lens.addEventListener("touchmove", moveLens);
//     img.addEventListener("touchmove", moveLens);
//     function moveLens(e) {
//         var pos, x, y;
//         /* Prevent any other actions that may occur when moving over the image */
//         e.preventDefault();
//         /* Get the cursor's x and y positions: */
//         pos = getCursorPos(e);
//         /* Calculate the position of the lens: */
//         x = pos.x - (lens.offsetWidth / 2);
//         y = pos.y - (lens.offsetHeight / 2);
//         /* Prevent the lens from being positioned outside the image: */
//         if (x > img.width - lens.offsetWidth) {x = img.width - lens.offsetWidth;}
//         if (x < 0) {x = 0;}
//         if (y > img.height - lens.offsetHeight) {y = img.height - lens.offsetHeight;}
//         if (y < 0) {y = 0;}
//         /* Set the position of the lens: */
//         lens.style.left = x + "px";
//         lens.style.top = y + "px";
//         /* Display what the lens "sees": */
//         result.style.backgroundPosition = "-" + (x * cx) + "px -" + (y * cy) + "px";
//     }
//     function getCursorPos(e) {
//         var a, x = 0, y = 0;
//         e = e || window.event;
//         /* Get the x and y positions of the image: */
//         a = img.getBoundingClientRect();
//         /* Calculate the cursor's x and y coordinates, relative to the image: */
//         x = e.pageX - a.left;
//         y = e.pageY - a.top;
//         /* Consider any page scrolling: */
//         x = x - window.pageXOffset;
//         y = y - window.pageYOffset;
//         return {x : x, y : y};
//     }
// }