(function(window) {
    "use strict";

    var mainContainer = document.querySelector(".main-wrap"),
        openCtrl = document.getElementById("btn-search"),
        openCtrl2 = document.getElementById("btn-search2"),
        closeCtrl = document.getElementById("btn-search-close"),
        searchContainer = document.querySelector(".search"),
        inputSearch = searchContainer ? searchContainer.querySelector(".search__input") : null;

    function init() {
        initEvents();
    }

    function initEvents() {
        if (openCtrl) {
            openCtrl.addEventListener("click", openSearch);
        }
        if (openCtrl2) {
            openCtrl2.addEventListener("click", openSearch);
        }
        if (closeCtrl) {
            closeCtrl.addEventListener("click", closeSearch);
        }
        document.addEventListener("keyup", function(ev) {
            // escape key.
            if (ev.keyCode == 27) {
                closeSearch();
            }
        });
    }

    function openSearch() {
        if (mainContainer) {
            mainContainer.classList.add("main-wrap--move");
        }
        if (searchContainer) {
            searchContainer.classList.add("search--open");
        }
        if (inputSearch) {
            setTimeout(function() {
                inputSearch.focus();
            }, 600);
        }
    }

    function closeSearch() {
        if (mainContainer) {
            mainContainer.classList.remove("main-wrap--move");
        }
        if (searchContainer) {
            searchContainer.classList.remove("search--open");
        }
        if (inputSearch) {
            inputSearch.blur();
            inputSearch.value = "";
        }
    }

    init();
})(window);

$(function() {
    setTimeout(function() {
        $(".loader").addClass("hide");
        $("html").addClass("loaded");
    }, 500);
});

$(document).ready(function() {
    $(".hamburger-box>.icon").click(function() {
        $(".hamburger-box>.icon").toggleClass("active");
    });
});

$(document).ready(function() {
    $("#mobile-menu").mCustomScrollbar({
        theme: "minimal"
    });

    $("#dismiss, .overlay").on("click", function() {
        $("#mobile-menu").removeClass("active");
        $("#sidebarCollapse").removeClass("active");
        $(".overlay").removeClass("active");
    });

    $("#sidebarCollapse").on("click", function() {
        $("#mobile-menu").addClass("active");
        $(".overlay").addClass("active");
        $(".collapse.in").toggleClass("in");
        $("a[aria-expanded=true]").attr("aria-expanded", "false");
    });

    $(".drp-mobile-link").on("click", function() {
        $(this)
            .next(".drp-mobile")
            .toggleClass("active");
    });
});

$(document).ready(function() {
    $(".header-dropdown .tab-link").on("click", function() {
        var target = $(this).attr("datatarget");

        $(target)
            .parent()
            .children(".tab-panel")
            .removeClass("active");
        $(target).addClass("active");

        $(this)
            .parent()
            .children(".tab-link")
            .removeClass("active");
        $(this).addClass("active");
    });

    $(".tab-component .tab-head .tab-link").on("click", function() {
        var target = $(this).attr("datatarget");

        $(".tab-component .tab-body .tab-panel").removeClass("active");
        $(target).addClass("active");

        $(".tab-component .tab-body .tab-panel .do-nicescroll3").removeClass(
            "active"
        );
        $(target + " .do-nicescroll3").addClass("active");
        $(target).addClass("active");

        $(".tab-component .tab-head .tab-link").removeClass("active");
        $(this).addClass("active");
    });
	
	$(".bagis-component .tab-head .tab-link").on("click", function() {
        var target = $(this).attr("datatarget");

        $(".bagis-component .tab-body .tab-panel").removeClass("active");
        $(target).addClass("active");

        $(".bagis-component .tab-body .tab-panel .do-nicescroll3").removeClass(
            "active"
        );
        $(target + " .do-nicescroll3").addClass("active");
        $(target).addClass("active");

        $(".bagis-component .tab-head .tab-link").removeClass("active");
        $(this).addClass("active");
    });

    $(".do-nicescroll3").niceScroll({
        cursorwidth: 8,
        cursoropacitymin: 0.6,
        cursorcolor: "#3b557a",
        cursorborder: "none",
        cursorborderradius: 4,
        autohidemode: "leave",
        emulatetouch: true,
        background: "#dde3e8",
        railoffset: { top: 20, left: -20 },
        cursorfixedheight: 250,
        scrollbarid: "etkinlik-scroll"
    });

    $(".owl-carousel-etkinlik").owlCarousel({
        loop: true,
        nav: true,
        items: 1,
        navContainer: ".etkinlik-nav"
    });
    $(".owl-carousel-proje").owlCarousel({
        loop: true,
        nav: true,
        items: 1,
        responsiveClass: true,
        navSpeed: 500,
		navText:['<i class="fas fa-chevron-left"></i>','<i class="fas fa-chevron-right"></i>'],
        navContainer: ".projeler-nav",
        responsive: {
            0: {
                stagePadding: 0,
                margin: 20
            },
            768: {
                stagePadding: 0,
                margin: 20
            },
            992: {
                stagePadding: 100
            },
            1200: {
                stagePadding: 250
            },
            1400: {
                stagePadding: 250
            },
            1600: {
                stagePadding: 400
            }
        }
    });

    $(".owl-carousel-festival").owlCarousel({
        loop: false,
        nav: true,
        items: 1,
        navContainer: ".festival-nav",
        rewind: true
    });

    $(".owl-carousel-hizlimenu").owlCarousel({
        loop: true,
		nav: true,
		autoplay:true,
        items: 10,
        stagePadding: 30,
        margin: 15,
        navContainer: ".hizlimenu-nav",
        responsive: {
            0: {
                nav: true,
                items: 2
            },
            768: {
                nav: true,
                items: 4
            },
            992: {
                nav: true,
                items: 5
            },
            1200: {
                nav: true,
                items: 7
            },
            1450: {
                nav: true,
                items: 8
            },
            1700: {
                items: 10,
                nav: true
            }
        }
    });

    $(".owl-carousel-fotogaleri").owlCarousel({
        loop: true,
        nav: true,
        items: 3,
        navContainer: ".fotogaleri-nav",
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 1
            },
            992: {
                items: 2
            },
            1200: {
                items: 3
            },
            1400: {
                items: 3
            },
            1600: {
                items: 3
            }
        }
    });
	
	$(".owl-carousel-videogaleri").owlCarousel({
        loop: true,
        nav: true,
        items: 3,
        navContainer: ".videogaleri-nav",
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 1
            },
            992: {
                items: 2
            },
            1200: {
                items: 3
            },
            1400: {
                items: 3
            },
            1600: {
                items: 3
            }
        }
    });

    $(".expert-slider").each(function(){
        var $slider = $(this);
        var $slides = $slider.find(".expert-slide");
        var $carousel = $slider.find(".expert-carousel");
        var $cards = $slides.find(".expert-card");
        if(!$slides.length){ return; }

        var autoInterval = parseInt($slider.data("interval"), 10) || 6500;
        var current = 0;
        var timer = null;

        var $dotsWrapper = $slider.find(".expert-slider-dots");
        var $dots = $();

        if($dotsWrapper.length){
            var dotsHtml = "";
            $slides.each(function(index){
                dotsHtml += '<button type="button" class="expert-dot' + (index === 0 ? ' active' : '') + '" data-index="'+index+'"></button>';
            });
            $dotsWrapper.html(dotsHtml);
            $dots = $dotsWrapper.find(".expert-dot");
        }

        function updateHeight(){
            if(!$slides.length){ return; }
            var $active = $slides.eq(current);
            if(!$active.length){ return; }
            var targetHeight = $active.outerHeight(true);
            if(targetHeight){
                $carousel.height(targetHeight);
            }
        }

        function goTo(index){
            if(index === current){ return; }
            $slides.removeClass("active").eq(index).addClass("active");
            if($dots.length){
                $dots.removeClass("active").eq(index).addClass("active");
            }
            current = index;
            updateHeight();
        }

        function next(){
            goTo((current + 1) % $slides.length);
        }

        function prev(){
            goTo((current - 1 + $slides.length) % $slides.length);
        }

        function startAuto(){
            if($slides.length < 2){ return; }
            stopAuto();
            timer = setInterval(next, autoInterval);
        }

        function stopAuto(){
            if(timer){
                clearInterval(timer);
                timer = null;
            }
        }
 
        $slider.find(".expert-slider-btn.next").on("click", function(){
            next();
            startAuto();
        });

        $slider.find(".expert-slider-btn.prev").on("click", function(){
            prev();
            startAuto();
        });

        if($dots.length){
            $dots.on("click", function(){
                var idx = $(this).data("index");
                if(typeof idx !== "undefined"){
                    goTo(idx);
                    startAuto();
                }
            });
        }

        $slider.on("mouseenter", stopAuto).on("mouseleave", startAuto);
        $cards.on("click", function(){
            if($(this).closest(".expert-slide").hasClass("active")){
                return;
            }
            var idx = $(this).closest(".expert-slide").index();
            if(idx >= 0){
                goTo(idx);
                startAuto();
            }
        });

        // Ensure first slide visible on load
        $slides.removeClass("active").eq(0).addClass("active");
        if($dots.length){
            $dots.removeClass("active").eq(0).addClass("active");
        }

        updateHeight();
        $slider.find("img").on("load", updateHeight);
        $(window).on("resize", updateHeight);
        startAuto();
    });

    var btn = $("#page-up");

    $(window).scroll(function() {
        if ($(window).scrollTop() > 200) {
            btn.addClass("active");
        } else {
            btn.removeClass("active");
        }
    });

    btn.on("click", function(e) {
        e.preventDefault();
        $("html, body").stop();
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
});

// Impact & Reach Counter Animation
function animateCounters() {
    $('.counter').each(function() {
        var $this = $(this);
        var countTo = $this.attr('data-count');
        
        if (countTo && !$this.hasClass('animated')) {
            $this.addClass('animated');
            
            $({ countNum: 0 }).animate({
                countNum: countTo
            }, {
                duration: 2000,
                easing: 'swing',
                step: function() {
                    $this.text(Math.floor(this.countNum));
                },
                complete: function() {
                    $this.text(this.countNum);
                }
            });
        }
    });
}

// Intersection Observer for counter animation
if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
            }
        });
    }, {
        threshold: 0.5
    });
    
    const impactSection = document.querySelector('.impact-reach-wrapper');
    if (impactSection) {
        observer.observe(impactSection);
    }
} else {
    // Fallback for older browsers
    $(window).on('scroll', function() {
        const impactSection = $('.impact-reach-wrapper');
        if (impactSection.length) {
            const elementTop = impactSection.offset().top;
            const elementBottom = elementTop + impactSection.outerHeight();
            const viewportTop = $(window).scrollTop();
            const viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                animateCounters();
            }
        }
    });
}