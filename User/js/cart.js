let allProducts = [];

fetch('get-products.php')
  .then(res => res.json())
  .then(data => {
    allProducts = data;
  });

function searchProducts() {
  const searchBox = document.getElementById("searchBox");
  const query = searchBox.value.toLowerCase();
  const searchResults = document.getElementById("searchResults");
  const priorityFruits = document.getElementById("priorityFruits");

  const filtered = allProducts.filter(product =>
    product.name.toLowerCase().includes(query)
  );

  searchResults.innerHTML = "";

  if (filtered.length > 0) {
    filtered.forEach(product => {
      const a = document.createElement("a");
      a.href = `itemInfo/${product.id}.html`; // tuỳ bạn cấu trúc URL
      a.innerText = product.name;
      a.className = "search-result";
      searchResults.appendChild(a);
    });
  } else {
    searchResults.innerHTML = "<span class='empty'>Không tìm thấy sản phẩm nào</span>";
  }

  searchResults.style.display = "block";

  setTimeout(() => {
    searchBox.value = "";
    searchResults.style.display = "none";
    if (priorityFruits) priorityFruits.style.display = "block";
  }, 5000);
}