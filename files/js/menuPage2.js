// ============ Functions ============
// ======== Functions for in the main functions ========
// Range slider value display
function rangeSliderValueListener(strRangeSliderID, strOutputID) {
    // ==== Declaring Variables ====

    // ==== Start of Function ====
    // Add event listener to the range slider
    document.getElementById(strRangeSliderID).addEventListener('input', function() {
        document.getElementById(strOutputID).innerHTML = this.value;
    });
}

// ============ Event Listeners ============
// Image preview

// ============ Start of Main ============
