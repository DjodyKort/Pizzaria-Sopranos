var MenuCheckbox = document.querySelectorAll('input[type="checkbox"]');

MenuCheckbox.forEach(function (checkbox) {
  checkbox.addEventListener("change", function () {
    getCheckboxValues();
  });
});

function getCheckboxValues() {
  var checkedCheckboxes = document.querySelectorAll(
    'input[type="checkbox"]:checked'
  );
  var values = [];
  checkedCheckboxes.forEach(function (checkbox) {
    values.push(checkbox.value);
  });
  document.getElementById("CartTextField").innerHTML = values.join(", <br>");

  console.log("Checkbox values: " + values.join(", "));
}
