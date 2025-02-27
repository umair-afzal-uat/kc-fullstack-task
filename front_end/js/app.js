// DOM Elements
const categoryList = document.getElementById('category-list');
const courseList = document.getElementById('course-list');

// API Endpoints
const CATEGORIES_API = 'http://api.cc.localhost/categories';
const COURSES_API = 'http://api.cc.localhost/courses';

// State
let allCategories = [];
let allCourses = [];

/**
 * Truncate text to a specified length and append ellipses if necessary.
 * @param {string} text - The text to truncate.
 * @param {number} maxLength - The maximum length of the text.
 * @returns {string} - The truncated text.
 */
function truncateText(text, maxLength) {
  return text.length > maxLength ? `${text.substring(0, maxLength)}...` : text;
}

/**
 * Fetch categories from the API and update the UI.
 * @returns {Promise<void>}
 */
async function fetchCategories() {
  try {
    const response = await fetch(CATEGORIES_API);
    const data = await response.json();
    allCategories = calculateCourseCounts(data, allCourses);
    renderCategories();
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
}

/**
 * Fetch courses from the API and update the UI.
 * @param {string|null} categoryId - The category ID to filter courses (optional).
 * @returns {Promise<void>}
 */
async function fetchCourses(categoryId = null) {
  try {
    const url = categoryId ? `${COURSES_API}?category_id=${categoryId}` : COURSES_API;
    const response = await fetch(url);
    const data = await response.json();
    allCourses = data;
    fetchCategories(); 
    renderCourses();
  } catch (error) {
    console.error('Error fetching courses:', error);
  }
}

/**
 * Calculate the number of courses per category, including parent categories.
 * @param {Array} categories - The list of categories.
 * @param {Array} courses - The list of courses.
 * @returns {Array} - Categories with updated course counts.
 */
function calculateCourseCounts(categories, courses) {
  const categoryMap = new Map();

  categories.data.forEach((category) => {
    categoryMap.set(category.id, { ...category, count_of_courses: category.count_of_courses });
  });

  courses.data.forEach((course) => {
    if (categoryMap.has(course.category_id)) {
      categoryMap.get(course.category_id).count_of_courses++;
    }
  });

  categories.data.forEach((category) => {
    let current = category;
    while (current.parent && categoryMap.has(current.parent)) {
      categoryMap.get(current.parent).count_of_courses += current.count_of_courses;
      current = categoryMap.get(current.parent);
    }
  });

  return Array.from(categoryMap.values());
}

/**
 * Build a category tree from a flat category list.
 * @param {Array} categories - The list of categories.
 * @returns {Array} - The category tree.
 */
function buildCategoryTree(categories) {
  let categoryMap = new Map();
  let tree = [];

  categories.forEach(category => {
    category.children = [];
    categoryMap.set(category.id, category);
  });

  categories.forEach(category => {
    if (category.parent_id) {
      let parent = categoryMap.get(category.parent_id);
      if (parent) {
        parent.children.push(category);
      }
    } else {
      tree.push(category);
    }
  });

  return tree;
}

/**
 * Render the category tree recursively.
 * @param {Array} categories - The category tree.
 * @param {number} depth - The indentation depth.
 * @returns {string} - The generated HTML.
 */
function renderCategoryTree(categories, depth = 0) {
  return categories.map(category => `
      <li onclick="filterCourses('${category.id}')" style="padding-left: ${depth * 20}px">
          ${category.name} (${category.count_of_courses})
      </li>
      ${category.children.length ? renderCategoryTree(category.children, depth + 1) : ''}
  `).join('');
}

/**
 * Render the category list in the UI.
 */
function renderCategories() {
  let tree = buildCategoryTree(allCategories);
  categoryList.innerHTML = `
      <li onclick="filterCourses(null)">All Categories</li>
      ${renderCategoryTree(tree)}
  `;
}

/**
 * Render the course list in the UI.
 */
function renderCourses() {
  if (allCourses.length === 0) {
    courseList.innerHTML = '<p>No courses available.</p>';
    return;
  }

  const isDesktop = window.matchMedia('(min-width: 768px)').matches;

  courseList.innerHTML = allCourses.data
    .map((course) => {
      const mainCategoryName = course ? course.main_category_name : 'Unknown Category';
      const truncatedTitle = isDesktop ? truncateText(course.title, 50) : course.title;
      const truncatedDescription = isDesktop ? truncateText(course.description, 100) : course.description;

      return `
        <div class="course-card">
          <img src="${course.image_preview}" alt="${course.title}" />
          <h3>${truncatedTitle}</h3>
          <p>${truncatedDescription}</p>
          <small>Main Category: ${mainCategoryName}</small>
        </div>
      `;
    })
    .join('');
}

/**
 * Filter courses based on selected category.
 * @param {string|null} categoryId - The selected category ID.
 */
function filterCourses(categoryId) {
  fetchCourses(categoryId);
}

/**
 * Initialize the application.
 */
function initApp() {
  fetchCourses();
}

// Start the application
initApp();
