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

// ============ Event Listeners ============
// Image preview
imagePreviewListener('idMainMedia', 'idImgPreview');

// ============ Start of Main ============
