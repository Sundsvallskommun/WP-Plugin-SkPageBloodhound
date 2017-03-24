/**
 *
 *  Case insensitive blood hound hunting down pages
 *  starting with user provided prefix
 */

var ulWrapper = "#bloodhound-pages";

(function( $ ) {

	'use strict';

    var inputLabelWrapper = null;
    var closeButtonAdded = false;

    $(document).ready(function() {

        var bloodHound = null;
        var bloodHoundPages = null;
        var inputElement = replaceWpDefaultParentField();


        fetchPageList(function(response) {

            $(inputElement).after(response.list);
            bloodHound = $("#page-bloodhound-input");
            bloodHoundPages = $(ulWrapper);

            bloodHound.val(response.parent_title);

            $('#bloodhound-spinner').remove();
            bloodHound.prop('disabled', false);
            bloodHound.removeClass('no-access');

            var listItems = bloodHoundPages.find("li");

            /**
             * Filter on keydown
             */
            bloodHound.keyup(function(event) {

                var filter = $(this).val();

                if (filter.length === 0 ) {
                    // Hide all item
                    // need a new listener for this
                    showListItems();
                }
                if (filter.length === 1 ) {
                    // Hide all item
                    // need a new listener for this
                    hideElements(listItems);
                }
                if (filter.length < 2 ) {
                    return;
                }

                $(listItems).each(function( index ) {
                    var liElement = $(this);
                    //var text = liElement.text();
                    var text = liElement.attr('data-text');
                    //text = text.trim();

                    if (text.toLowerCase().indexOf(filter.toLowerCase()) !== -1) {
                        liElement.addClass('show');
                        showBloodHoundTrail(liElement);
                    }
                    else {
                        liElement.removeClass('show');
                    }
                });

            });


            /**
             *
             * Show complete list when clicking input field
             */
            bloodHound.click(function(event) {

                if (!closeButtonAdded) {
                    bloodHound.after('<span title="stäng" id="close-bloodhound" class="close-bloodhound">&times</span>');
                    closeButtonAdded = true;
                }

                showListItems();

            });

            function showListItems() {

                bloodHoundPages.addClass('show');
                $(listItems).each(function( index ) {
                    var element = $(this);
                    element.addClass('show');
                });
            }

            /**
             *
             * close list when clicking close button
             */
            $("#pageparentdiv").on('click', '#close-bloodhound', function(event) {
                bloodHoundPages.removeClass('show');
            });




        });

        /**
         *
         * Click listener when selecting page
         */
        $("#pageparentdiv").on('click', 'a', function(event) {
            event.preventDefault();
            var element = $(this);
            setBloodHoundValue(element.text(), element.attr('data-page-id'));
            bloodHoundPages.removeClass('show');
            bloodHound.focus();

        });

        /**
         *
         * Sets chosen page in the bloodhound input field
         */
        function setBloodHoundValue(displayName, value) {
            bloodHound.attr('data-page-id', value);
            bloodHound.val(displayName);

            $('#hidden-page-bloodhound-input').val(value);

        }

    });



    /**
     *
     *  Case insensitive selector instead of :contains
     */
    $.extend($.expr[':'], {
      'containsi': function(elem, i, match, array)
      {
        return (elem.textContent || elem.innerText || '').toLowerCase()
        .indexOf((match[3] || "").toLowerCase()) >= 0;
      }
    });




    /**
     *
     * Adds css to display the full trail a.k.a exit path of current element
     *
     * @param element the element to show the trail for
     */
    function showBloodHoundTrail(element) {
        element.parents().addClass('show');
    }


    function hideElements(list) {
        $(list).each(function( index ) {
            $(this).removeClass('show');
        });
    }

    /**
     *
     * Replaces the default wp input field for parent selection
     *
     * NOTE: Two inputs are used - one for display and one for setting value
     *
     * with the bloodhound field
     *
     * @return String the id of the new input field
     */
    function replaceWpDefaultParentField() {

        // Yes this is the input field even tho the id sucks
        var defautlFieldElement = $('#parent_id');

        defautlFieldElement.remove();
        inputLabelWrapper = $('#pageparentdiv label[for="parent_id"]').parent();

        inputLabelWrapper.after('<input id="hidden-page-bloodhound-input" name="parent_id" type="text"/>');
        inputLabelWrapper.after('<input id="page-bloodhound-input" autocomplete="off"  class="no-access" disabled type="text" placeholder=""/><span id="bloodhound-spinner" class="spinner bloodhound-spinner is-active"></span>');
        inputLabelWrapper.after('<p class="howto" id="new-tag-search_alias-desc">Börja skriv namnet på den överordnande sidan</p>');


        return "#hidden-page-bloodhound-input";
    }

    /**
     *
     * Fetching the page list from wp endpoint and adding it after provided element.
     * Parent ID is also retrieved here
     *
     * @param element list will be added after this element
     * @param callback
     */
    function fetchPageList(callback){

        var data = {
		    'action': bloodhound_ajax_object.action,
		    'current_post' : bloodhound_ajax_object.current_post
        };

        jQuery.post(bloodhound_ajax_object.ajax_url, data, function(response) {
            callback(response);
        });


    }





})(jQuery);

