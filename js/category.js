// Helper: Validate category name (string, not empty, no special chars)
function validateCategoryName(name) {
  return typeof name === 'string' && /^[a-zA-Z0-9 ]+$/.test(name) && name.trim().length > 0;
}

// AJAX helper
function ajaxAction(url, data, callback) {
  fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
  })
  .then(res => res.json())
  .then(callback)
  .catch(err => {
    console.error('AJAX Error:', err);
    alert('Network error. Please try again.');
  });
}


// CREATE
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("categoryForm");
  const input = document.getElementById("category_name");

  form.addEventListener("submit", function (e) {
    e.preventDefault(); // stop normal form submission
    const name = input.value.trim();

    addCategory(name); // call your existing function
  });
});

function addCategory(name) {
  if (!validateCategoryName(name)) {
    alert('Invalid category name.');
    return;
  }
  ajaxAction('../actions/add_category_action.php', { name }, res => {
    alert(res.success ? 'Category added!' : 'Error: ' + res.message);
    if (res.success) location.reload();
  });
}


// UPDATE
document.addEventListener("DOMContentLoaded", () => {
  // Attach submit handler to ALL update forms
  document.querySelectorAll(".updateCategoryForm").forEach(form => {
    form.addEventListener("submit", function (e) {
      e.preventDefault(); // prevent normal submission

      const id = this.dataset.id;
      const name = this.querySelector(".new_name").value.trim();

      updateCategory(id, name); // call your existing function
    });
  });
});

function updateCategory(id, new_name) {
  if (!validateCategoryName(new_name)) {
    alert('Invalid category name.');
    return;
  }
  ajaxAction('../actions/update_category_action.php', { id, new_name }, res => {
    alert(res.success ? 'Category updated!' : 'Error: ' + res.message);
    if (res.success) location.reload();
  });
}


// DELETE
 document.querySelectorAll(".deleteCategoryForm").forEach(form => {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      const id = this.dataset.id;

      if (confirm("Delete this category?")) {
        deleteCategory(id);
      }
    });
  });

function deleteCategory(id) {
  ajaxAction('../actions/delete_category_action.php', { id }, res => {
    if (res.success) {
      alert('Category deleted!');
      document.querySelector(`.deleteCategoryForm[data-id="${id}"]`)
              .closest('tr')
              .remove(); // remove row from table
    } else {
      alert('Error: ' + res.message);
    }
  });
}


// RETRIEVE (example usage)
function getCategories() {
  fetch('../actions/fetch_category_action.php')
    .then(res => res.json())
    .then(data => {
      // Render categories or handle data
      console.log(data);
    })
    .catch(() => alert('Failed to retrieve categories.'));
}