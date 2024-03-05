// ============ Functions ============
// ======== Functions for in the main functions ========
// Image preview
function imagePreviewListener(strInputFieldID, strImgPreviewID) {
    // ==== Declaring Variables ====

    // ==== Start of Function ====
    // Add event listener to the input field

    document.getElementById(strInputFieldID).addEventListener('change', function(e) {
        var preview = document.getElementById(strImgPreviewID);
        var file = e.target.files[0];
        var reader = new FileReader();

        reader.onloadend = function() {
            preview.src = reader.result;
            preview.style.display = 'block';
            preview.style.width = '40vw';

            // Border
            preview.style.border = '1px solid #000';
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "";
        }
    });
}

// Range slider value display
function rangeSliderValueListener(strRangeSliderID, strOutputID) {
    // ==== Declaring Variables ====

    // ==== Start of Function ====
    // Add event listener to the range slider
    document.getElementById(strRangeSliderID).addEventListener('input', function() {
        document.getElementById(strOutputID).innerHTML = this.value;
    });
}

// Sync the discount percentage with the discount price
function syncDiscountPrice(strDiscountPercentageID, strDiscountPriceID, strOriginalPriceID) {
    window.addEventListener('DOMContentLoaded', (event) => {
        // Elements
        const discountPercentage = document.getElementById(strDiscountPercentageID);
        const discountPrice = document.getElementById(strDiscountPriceID);
        const originalPrice = document.getElementById(strOriginalPriceID);

        // Add event listener to the discount percentage
        discountPercentage.addEventListener('input', function() {
            // Parse input values as numbers
            let discountPercentageValue = parseFloat(discountPercentage.value);
            let originalPriceValue = parseFloat(originalPrice.value);

            // Checking if the discount percentage is more than 100
            if (discountPercentageValue > 100) {
                discountPercentage.value = 100;
            }
            else {
                // Calculate the discount price
                discountPrice.value = (originalPriceValue * (discountPercentageValue / 100)).toFixed(2);
            }
        });

        // Add event listener to the discount price
        discountPrice.addEventListener('input', function() {
            // Parse input values as numbers
            let discountPriceValue = parseFloat(discountPrice.value);
            let originalPriceValue = parseFloat(originalPrice.value);

            // Checking if the discount price is more than the original price
            if (discountPriceValue > originalPriceValue) {
                discountPrice.value = originalPriceValue.toFixed(2);
            }
            else {
                // Calculate the discount percentage
                discountPercentage.value = ((discountPriceValue / originalPriceValue) * 100).toFixed(2);
            }
        });

        // Add event listener to the original price
        originalPrice.addEventListener('input', function() {
            // Parse input values as numbers
            let discountPercentageValue = parseFloat(discountPercentage.value);
            let originalPriceValue = parseFloat(originalPrice.value);

            // Calculate the discount price
            discountPrice.value = (originalPriceValue * (discountPercentageValue / 100)).toFixed(2);

            // Calculate the discount percentage
            discountPercentage.value = ((discountPrice.value / originalPriceValue) * 100).toFixed(2);
        });
    });
}

// ============ Event Listeners ============
// Image preview
imagePreviewListener('idMainMedia', 'idImgPreview');
rangeSliderValueListener('idSpicyRating', 'idSpicyValue');
syncDiscountPrice('idDiscountPercentage', 'idDiscountPrice', 'idPrice');

// ============ Start of Main ============
