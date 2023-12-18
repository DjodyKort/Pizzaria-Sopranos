const Pizzas = [
  "Pizza Salami",
  "Pizza Margherita",
  "Pizza Funghi",
  "Pizza Calzone",
  "Pizza Prosciutto",
  "Pizza Quattro Formaggi",
  "Pizza Verdure",
  "Pizza Marinara",
];
const PizzaPrices = [
  "€11,50",
  "€9,99",
  "€12,99",
  "€13,50",
  "€13,50",
  "€12,50",
  "€10,50",
  "€11,99",
];

for (let i = 0; i < Pizzas.length; i++) {
  var menuItemId = "MenuItem" + i;
  createElement(menuItemId);
  function createElement(menuItemId) {


    const MenuContainer = document.getElementById("MenuContainer");
    const MenuItem = document.createElement("div");
    MenuItem.id = menuItemId;
    MenuContainer.appendChild(MenuItem);

    const PizzaImg = document.createElement("img");
    PizzaImg.src = "../../images/pizza.png";
    PizzaImg.alt = Pizzas[i];
    PizzaImg.className = "PizzaImg";
    MenuItem.appendChild(PizzaImg);

    const MenuPizzaName = document.createElement("h1");
    MenuPizzaName.className = "MenuText Pizza";
    MenuPizzaName.textContent = Pizzas[i];
    MenuItem.appendChild(MenuPizzaName);

    const MenuPizzaPrice = document.createElement("h1");
    MenuPizzaPrice.className = "MenuText Prijs";
    MenuPizzaPrice.textContent = PizzaPrices[i];
    MenuItem.appendChild(MenuPizzaPrice);

    const MenuInputs = document.createElement("div");
    MenuInputs.id = "MenuInputs";
    MenuItem.appendChild(MenuInputs);

    const MenuInputText = document.createElement("input");
    MenuInputText.id = "MenuItem" + i + "Input";
    MenuInputText.className = "MenuInputText";
    MenuInputText.type = "text";
    MenuInputText.value = "0";
    MenuInputs.appendChild(MenuInputText);

    const MenuButton = document.createElement("input");
    MenuButton.addEventListener("click", function () {
      AddCart(menuItemId);
    });
    MenuButton.className = "MenuButton";
    MenuButton.type = "button";
    MenuButton.value = "add";
    MenuInputs.appendChild(MenuButton);
  }
}

function AddCart(e) {
  var ItemId = document.getElementById(e);
  if (e != null) {
    var h1Element = ItemId.querySelector(".Pizza");
    var h1Prijs = ItemId.querySelector(".Prijs");
    var PizzaName = h1Element.textContent;
    var PizzaPrice = parseFloat(
      h1Prijs.textContent.replace("€", "").replace(/,/g, ".")
    );
    var PizzaAmount = document.getElementById(e + "Input");
    var PizzaAmountText = parseFloat(PizzaAmount.value);

    var PizzaPriceTotal = 0;

    PizzaPriceTotal = (PizzaPrice * PizzaAmountText).toFixed(2);

    var PizzaPriceTotalString = String(PizzaPriceTotal).replace(".", ",");
    console.log(PizzaPriceTotal);
    document.getElementById("CartTextField").textContent = PizzaName;
    document.getElementById("CartTextAmount").textContent = PizzaAmountText;
    document.getElementById("CartPrice").textContent =
      "€" + PizzaPriceTotalString;
  } else {
    console.log("error het werkt niet");
  }
}
