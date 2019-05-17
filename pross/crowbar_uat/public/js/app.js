$(document).ready(function() {
	
	// Employer Menu
	$('.mobile-menu').on('click', function(e){
		e.stopPropagation();
        $('.usermenu-submenu').slideUp('100');
        $('.notification-submenu').slideUp('100');
        $('.navigation-group-list').slideToggle('200');
	});

	//Show Skills
	$('.show-more').on('click', function(){
		$('.skills-tag a').fadeIn('200');
		$(this).hide();
	})


    //User Menu
    $('#usermenu-toggle').on('click', function(e){
        e.stopPropagation();
        $('.notification-submenu').slideUp('100');
        $('.usermenu-submenu').slideToggle('200');
    });
    $('body').on('click', function(e) {
        var target = $(e.target);
        if (!target.is('.usermenu-submenu') && !target.is('.usermenu-submenu *')) {
            if ($('.usermenu-submenu').is(':visible')) {
                $('.usermenu-submenu').slideUp('200');
            } 
        }
    });

    //User Menu
    $('.notification-toggle').on('click', function(e){
        e.stopPropagation();
        $('.usermenu-submenu').slideUp('100');
        $('.notification-submenu').slideUp('100');
        if (!($(this).siblings('.notification-submenu').is(':visible'))) {
            $(this).siblings('.notification-submenu').slideDown('200');
        }
    });
    $('body').on('click', function(e) {
        var target = $(e.target);
        if (!target.is('.notification-submenu') && !target.is('.notification-submenu *')) {
            if ($('.notification-submenu').is(':visible')) {
                $('.notification-submenu').slideUp('100');
            } 
        }
    });

	//Profile Tabs
    if(typeof easyResponsiveTabs != 'undefined'){
        $('#responsive-tab').easyResponsiveTabs({
            type: 'default', //Types: default, vertical, accordion
            width: 'auto', //auto or any width like 600px
            fit: true, // 100% fit in a container
            closed: 'accordion', // Start closed if in accordion view
            tabidentify: 'hor_1', // The tab groups identifier
        });
    }


    //UncheckAll
    $('#clearAll').on('click', function(event) {
       $('.filter-options input[type="checkbox"]').each( function(){
            $(this).prop( "checked", false );
       });
    });

    //Price Slider
    if($('.nstSlider').length > 0){
        $('.nstSlider').nstSlider({
            "left_grip_selector": ".leftGrip",
            "right_grip_selector": ".rightGrip",
            "value_bar_selector": ".bar",
            "value_changed_callback": function(cause, leftValue, rightValue) {
                var $container = $(this).parent();

                $container.find('.leftLabel.form-control').text(leftValue);
                $container.find('.leftLabel').next().val(leftValue).trigger('keyup');
                $container.find('.rightLabel.form-control').text(rightValue);
                $container.find('.rightLabel').next().val(rightValue).trigger('keyup');
            }
        });
    }


    //Common DatePicker
    if($('.timelineDatepicker').length > 0){
        $('.timelineDatepicker').datetimepicker({
            format: 'DD/MM/YYYY'
        });
    }


    //Filter Menu
    $('.sidebar-menu').on('click', function(e){
        e.stopPropagation();
        $('#left-sidebar').slideToggle('400');
    });


    //Job Description
    $('#hide-description input').on('change', function() {
        if ($(this).is(':checked')) { 
            $('.content-box-description p').slideDown('200'); 
        } else { 
            $('.content-box-description p').slideUp('200'); 
        } 
    });
}); 







//Drag n Drop Image Upload Section
'use strict';
;
(function(document, window, index) {
    // feature detection for drag&drop upload
    var isAdvancedUpload = function() {
        var div = document.createElement('div');
        return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
    }();


    // applying the effect for every form
    var forms = document.querySelectorAll('.box');
    Array.prototype.forEach.call(forms, function(form) {
        var input = form.querySelector('input[type="file"]'),
            label = form.querySelector('label'),
            errorMsg = form.querySelector('.box__error span'),
            restart = form.querySelectorAll('.box__restart'),
            droppedFiles = false,
            showFiles = function(files) {
                label.textContent = files.length > 1 ? (input.getAttribute('data-multiple-caption') || '').replace('{count}', files.length) : files[0].name;
            },
            triggerFormSubmit = function() {
                var event = document.createEvent('HTMLEvents');
                event.initEvent('submit', true, false);
                form.dispatchEvent(event);
            };

        // letting the server side to know we are going to make an Ajax request
        var ajaxFlag = document.createElement('input');
        ajaxFlag.setAttribute('type', 'hidden');
        ajaxFlag.setAttribute('name', 'ajax');
        ajaxFlag.setAttribute('value', 1);
        form.appendChild(ajaxFlag);

        // automatically submit the form on file select
        input.addEventListener('change', function(e) {
            showFiles(e.target.files);


            triggerFormSubmit();


        });

        // drag&drop files if the feature is available
        if (isAdvancedUpload) {
            form.classList.add('has-advanced-upload'); // letting the CSS part to know drag&drop is supported by the browser

            ['drag', 'dragstart', 'dragend', 'dragover', 'dragenter', 'dragleave', 'drop'].forEach(function(event) {
                form.addEventListener(event, function(e) {
                    // preventing the unwanted behaviours
                    e.preventDefault();
                    e.stopPropagation();
                });
            });
            ['dragover', 'dragenter'].forEach(function(event) {
                form.addEventListener(event, function() {
                    form.classList.add('is-dragover');
                });
            });
            ['dragleave', 'dragend', 'drop'].forEach(function(event) {
                form.addEventListener(event, function() {
                    form.classList.remove('is-dragover');
                });
            });
            form.addEventListener('drop', function(e) {
                droppedFiles = e.dataTransfer.files; // the files that were dropped
                showFiles(droppedFiles);


                triggerFormSubmit();

            });
        }


        // if the form was submitted
        form.addEventListener('submit', function(e) {
            // preventing the duplicate submissions if the current one is in progress
            if (form.classList.contains('is-uploading')) return false;

            form.classList.add('is-uploading');
            form.classList.remove('is-error');

            if (isAdvancedUpload) // ajax file upload for modern browsers
            {
                e.preventDefault();

                // gathering the form data
                var ajaxData = new FormData(form);
                if (droppedFiles) {
                    Array.prototype.forEach.call(droppedFiles, function(file) {
                        ajaxData.append(input.getAttribute('name'), file);
                    });
                }

                // ajax request
                var ajax = new XMLHttpRequest();
                ajax.open(form.getAttribute('method'), form.getAttribute('action'), true);

                ajax.onload = function() {
                    form.classList.remove('is-uploading');
                    if (ajax.status >= 200 && ajax.status < 400) {
                        var data = JSON.parse(ajax.responseText);
                        form.classList.add(data.success == true ? 'is-success' : 'is-error');
                        if (!data.success) errorMsg.textContent = data.error;
                    } else alert('Error. Please, contact the webmaster!');
                };

                ajax.onerror = function() {
                    form.classList.remove('is-uploading');
                    alert('Error. Please, try again!');
                };

                ajax.send(ajaxData);
            } else // fallback Ajax solution upload for older browsers
            {
                var iframeName = 'uploadiframe' + new Date().getTime(),
                    iframe = document.createElement('iframe');

                $iframe = $('<iframe name="' + iframeName + '" style="display: none;"></iframe>');

                iframe.setAttribute('name', iframeName);
                iframe.style.display = 'none';

                document.body.appendChild(iframe);
                form.setAttribute('target', iframeName);

                iframe.addEventListener('load', function() {
                    var data = JSON.parse(iframe.contentDocument.body.innerHTML);
                    form.classList.remove('is-uploading')
                    form.classList.add(data.success == true ? 'is-success' : 'is-error')
                    form.removeAttribute('target');
                    if (!data.success) errorMsg.textContent = data.error;
                    iframe.parentNode.removeChild(iframe);
                });
            }
        });


        // restart the form if has a state of error/success
        Array.prototype.forEach.call(restart, function(entry) {
            entry.addEventListener('click', function(e) {
                e.preventDefault();
                form.classList.remove('is-error', 'is-success');
                input.click();
            });
        });

        // Firefox focus bug fix for file input
        input.addEventListener('focus', function() { input.classList.add('has-focus'); });
        input.addEventListener('blur', function() { input.classList.remove('has-focus'); });

    });
}(document, window, 0));



$(window).on('load', function(){
    if(typeof fancybox != 'undefined'){
        $('[data-fancybox]').fancybox({
            buttons : [
                'zoom',
            'close'
            ]
        });
    }
});