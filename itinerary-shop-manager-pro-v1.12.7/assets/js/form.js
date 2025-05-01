document.addEventListener("DOMContentLoaded", function () {
    if (window.jQuery) {
        jQuery(".select2").select2();
    }

    const addBtn = document.getElementById("add-shop-item");
    const tbody = document.querySelector("#shop-items-body");
    const unitMap = {
        weight: ["Gram", "Kilogram"],
        quantity: ["Piece"],
        litre: ["Millilitre", "Litre"],
        length: ["Millimetre", "Centimetre", "Metre"],
        "L*B": ["Square mm", "Square cm", "Square metre"]
    };

    addBtn?.addEventListener("click", function (e) {
        e.preventDefault();
        const index = tbody.children.length;
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td><input name="shop_items[${index}][name]" /></td>
            <td>
                <select name="shop_items[${index}][type]" class="item-type">
                    ${Object.keys(unitMap).map(type => `<option value="${type}">${type}</option>`).join("")}
                </select>
            </td>
            <td><input name="shop_items[${index}][quantity]" /></td>
            <td>
                <select name="shop_items[${index}][unit]" class="item-unit">
                    ${unitMap["weight"].map(u => `<option value="${u}">${u}</option>`).join("")}
                </select>
            </td>
            <td><input name="shop_items[${index}][price]" /></td>
            <td><input type="file" name="shop_items_images[${index}]" accept="image/*" /></td>
            <td><a href="#" class="remove-item">✖</a></td>
        `;
        tbody.appendChild(tr);
    });

    document.body.addEventListener("change", function (e) {
        if (e.target.classList.contains("item-type")) {
            const row = e.target.closest("tr");
            const unitSelect = row.querySelector(".item-unit");
            const type = e.target.value;
            unitSelect.innerHTML = unitMap[type].map(u => `<option value="${u}">${u}</option>`).join("");
        }
    });

    document.body.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-item")) {
            e.preventDefault();
            e.target.closest("tr")?.remove();
        }
    });
});