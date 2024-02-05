<?php
// ============ Imports ============
# Internally
require_once('./files/php/functions.php');
require_once('./files/php/classes.php');

// ============ Declaring Variables ============

// ============ Start of Program ============
# Header
Functions::htmlHeader(380);

# POST Request

# Body
echo("
<div class='container'>
    <div class='row justify-content-center'>
        <div class='col-6 border border-dark rounded'>
            <div class='container-fluid mt-4'>
                <form method='POST' action='".Functions::dynamicPathFromIndex()."files/php/pages/menu.php'>
                    <!-- Buttons -->
                    <div class='row justify-content-center mb-5'>
                        <!-- First column with delivery button -->
                        <div class='col-md-5 col-5 d-flex'>
                            <button name='nameButtonLevering' type='button' class='buttonIndex'>
                                <img src='".Functions::dynamicPathFromIndex()."files/images/scooter.png' alt='Levering' width='50' height='50'><br/>
                                <p>Levering</p>
                            </button>
                        </div>
                        
                        <!-- Second column with takout button -->
                        <div class='col-md-5 col-5 d-flex justify-content-end'>
                            <button name='nameButtonTakeout' type='button' class='buttonIndex'>
                                <img src='".Functions::dynamicPathFromIndex()."files/images/pizza-box.png' alt='Takeout' width='50' height='50'><br/>
                                <p>Takeout</p>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Text field(s) -->
                    <div class='divIndexTextFields row justify-content-center'>
                        <!-- text field -->
                        <div class='divTextIndexField1 col-lg-10 col-md-10 col-sm-12 d-flex'>
                            <input type='text' class='form-control' placeholder='Zoek naar postcode' aria-label='Postcode' aria-describedby='button-addon2'>
                        </div>
                    </div>
                    
                    <!-- Submit button -->
                    <div class='row justify-content-center mt-5 mb-4'>
                        <div class='col-10 d-flex justify-content-center'>
                            <button type='submit' class='buttonIndexSubmit d-flex justify-content-center align-items-center btn w-100'>
                                <p style='margin: auto;'>Locatie gebruiken</p>
                                <img src='".Functions::dynamicPathFromIndex()."files/images/location-arrow.svg' alt='Locatie' height='40'>
                            </button>
                        </div>
                    </div>
                </form>
            </div>   
        </div>
    </div>
</div>
");

# Scripts
echo("
<script>
$(document).ready(function(){
    // ==== Declaring Variables ====
    // HTML Elements
    const buttonIndex = $('.buttonIndex');
    const buttonIndexSubmit = $('.buttonIndexSubmit');
    const divTextIndexFields = $('.divIndexTextFields');
    const divTextIndexField1 = $('.divTextIndexField1');
    
    // Dynamic Strings
    const strLeveringParagraph = 'Bestellen';
    const strLeveringImgSource = '".Functions::dynamicPathFromIndex()."files/images/arrow-right.svg';
    
    const strTakeoutParagraph = 'Locatie gebruiken';
    const strTakeoutImgSource = '".Functions::dynamicPathFromIndex(). "files/images/location-arrow.svg';
    
    // ==== Event Listeners ====
    buttonIndex.click(function(){
        $('.buttonIndex').removeClass('buttonIndexActive');
        $(this).addClass('buttonIndexActive');
        
        // Change text and image of submit button
        if($(this).attr('name') === 'nameButtonLevering'){
            // Changing p
            buttonIndexSubmit.children('p').text(strLeveringParagraph);
            // Changing img
            buttonIndexSubmit.children('img').attr('src', strLeveringImgSource);
            
            // Changing the col-10 to col-8
            divTextIndexField1.removeClass('col-lg-10 col-md-10 col-sm-12');
            divTextIndexField1.addClass('col-lg-8 col-md-8 col-sm-12');
            // Adding an extra input field for the house number
            divTextIndexFields.append('<div style=\'padding: 0;\' class=\'inputIndexHouseNumber col-lg-2 col-md-2 col-sm-12\'><input type=\'text\' class=\'form-control\' placeholder=\'Nr.\' aria-label=\'Nr.\' aria-describedby=\'button-addon2\'></div>');

        }
        else if($(this).attr('name') === 'nameButtonTakeout'){
            // Removing the extra input field for the house number
            divTextIndexFields.children('.inputIndexHouseNumber').remove();
            // Changing the col-8 to col-10
            divTextIndexField1.removeClass('col-lg-8 col-md-8 col-sm-12');
            divTextIndexField1.addClass('col-lg-10 col-md-10 col-sm-12');
            
            // Changing p
            buttonIndexSubmit.children('p').text(strTakeoutParagraph);
            // Changing img
            buttonIndexSubmit.children('img').attr('src', strTakeoutImgSource);
        }
    });
    
    // ==== Start of Program ====
    buttonIndex.last().click();
});
</script>
");

# Footer
Functions::htmlFooter();