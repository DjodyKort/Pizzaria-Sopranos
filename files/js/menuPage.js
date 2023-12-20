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

var i = 0;

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
    MenuInputText.id = menuItemId + "Input";
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
    if (i == 999) {
      i = 0;
    } else {
      i++;
    }

    CreateCartLine();
    CalcTotal();
    InsertToCart(e, ItemId);
  } else {
    console.log("error het werkt niet");
  }
}

function CartDelete(pp) {
  var IdContainer = pp.replace("CartDelete", "OrderContainerId")
  var snake = document.getElementById(IdContainer)
    if (snake) {
      snake.remove();
    } else {
        console.log("Error: Element not found" + IdContainer);
    }
}

function InsertToCart(crafter, miner) {
  var h1Element = miner.querySelector(".Pizza");
  var h1Prijs = miner.querySelector(".Prijs");
  var PizzaName = h1Element.textContent;
  var PizzaPrice = parseFloat(
    h1Prijs.textContent.replace("€", "").replace(/,/g, ".")
  );
  var PizzaAmount = document.getElementById(crafter + "Input");
  var PizzaAmountText = parseFloat(PizzaAmount.value);

  var PizzaPriceTotal = 0;

  PizzaPriceTotal = (PizzaPrice * PizzaAmountText).toFixed(2);

  var PizzaPriceTotalString = String(PizzaPriceTotal).replace(".", ",");
  brazy2 = document.getElementById("CartTextField" + i).textContent = PizzaName;
  brazy3 = document.getElementById("CartTextAmount" + i).textContent =
    PizzaAmountText;
  brazy4 = document.getElementById("CartPrice" + i).textContent =
    "€" + PizzaPriceTotalString;
}

function CalcTotal() {
  var TotalPizzaCount = document.querySelectorAll(".CalcPrice");
  var Pricetotal = document.getElementById("Pricetotal");
  var CalcPrice = TotalPizzaCount.textContent;
  // console.log(TotalPizzaCount, CalcPrice);
  TotalPizzaCount.forEach(() => {
    CalcPrice;
  });
  // console.log(CalcPrice);
  Pricetotal.textContent = CalcPrice;
}

function CreateCartLine() {
  const brazy0 = document.getElementById("CartOrdercontainer");
  const brazy1 = document.createElement("div");
  var brazy2 = document.createElement("h1");
  var brazy3 = document.createElement("h1");
  var brazy4 = document.createElement("h1");
  var brazy5 = document.createElement("button");

  brazy1.className = "OrderContainer OrderContainerId" + i;
  brazy2.className = "MenuText CartOrder OrderSelector";
  brazy3.className = "MenuText CartOrder";
  brazy4.className = "MenuText CartOrder CalcPrice";
  brazy5.className = "CartDelete";

  brazy1.id = "OrderContainerId" + i;
  brazy2.id = "CartTextField" + i;
  brazy3.id = "CartTextAmount" + i;
  brazy4.id = "CartPrice" + i;
  brazy5.id = "CartDelete" + i;


  brazy5.textContent = "Delete";

  brazy0.appendChild(brazy1);
  brazy1.appendChild(brazy2);
  brazy1.appendChild(brazy3);
  brazy1.appendChild(brazy4);
  brazy1.appendChild(brazy5);

  brazy5.addEventListener("click", function () {
    var wtf = document.querySelector('[id^="CartDelete"]').id;
    CartDelete(wtf);
  });
}
