// ============ Functions ============
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
    // ==== Declaring Variables ====
    // Elements
    const discountPercentage = document.getElementById(strDiscountPercentageID);
    const discountPrice = document.getElementById(strDiscountPriceID);
    const originalPrice = document.getElementById(strOriginalPriceID);

    // ==== Start of Function ====
    // Add event listener to the discount percentage
    discountPercentage.addEventListener('input', function() {
        let percentage = discountPercentage.value;
        if (percentage > 100) {
            percentage = 100;
            discountPercentage.value = percentage;
        }
        discountPrice.value = originalPrice.value * (percentage / 100);
    });

    // Add event listener to the discount price
        discountPrice.addEventListener('input', function() {
            let price = discountPrice.value;
            if (price > originalPrice.value) {
                price = originalPrice.value;
                discountPrice.value = price;
            }
            discountPercentage.value = price / originalPrice.value * 100;
        });

    // Add event listener to the original price
        originalPrice.addEventListener('input', function() {
            let price = originalPrice.value;
            let discount = price * (discountPercentage.value / 100);
            if (discount > price) {
                discount = price;
                discountPrice.value = discount;
            } else {
                discountPrice.value = discount;
            }
        });
}


// ============ Event Listeners ============
// Image preview
imagePreviewListener('idMainMedia', 'idImgPreview');
rangeSliderValueListener('idSpicyRating', 'idSpicyValue');
syncDiscountPrice('idDiscountPercentage', 'idDiscountPrice', 'idPrice');

// ============ Start of Main ============
