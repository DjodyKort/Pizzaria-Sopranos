// ============ Functions ============
// Function to add number to passcode input
function addValueToPasscodeInput(idPasscodeInput, strValue) {
    // ======== Declaring variables ========
    const passcodeInput = document.getElementById(idPasscodeInput);

    // ======== Start of Function ========
    passcodeInput.value += strValue;
}

// Function to remove last number from passcode input
function removeLastValueFromPasscodeInput(idPasscodeInput) {
    // ======== Declaring variables ========
    const passcodeInput = document.getElementById(idPasscodeInput);

    // ======== Start of Function ========
    passcodeInput.value = passcodeInput.value.slice(0, -1);
}

// Function to clear passcode input
function clearPasscodeInput(idPasscodeInput) {
    let passcodeInput = document.getElementById(idPasscodeInput);
    passcodeInput.value = '';
}