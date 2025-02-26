// DOM Elements
const categoryList = document.getElementById('category-list');
const courseList = document.getElementById('course-list');

// API Endpoints
const CATEGORIES_API = 'http://api.cc.localhost/categories';
const COURSES_API = 'http://api.cc.localhost/courses';

// State
let allCategories = [];
let allCourses = [];

// Utility function to truncate text with ellipses
function truncateText(text, maxLength) {
  return text.length > maxLength ? `${text.substring(0, maxLength)}...` : text;
}

// Fetch Categories
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

// Fetch Courses
async function fetchCourses(categoryId = null) {
  try {
    const url = categoryId ? `${COURSES_API}?category_id=${categoryId}` : COURSES_API;
    const response = await fetch(url);
    const data = await response.json();
    allCourses = data;
    fetchCategories(); // Recalculate course counts after fetching courses
    renderCourses();
  } catch (error) {
    console.error('Error fetching courses:', error);
  }
}

// Calculate Course Counts for Categories (Including Child Categories)
function calculateCourseCounts(categories, courses) {
  const categoryMap = new Map();

  // Initialize counts
  categories.data.forEach((category) => {
    categoryMap.set(category.id, { ...category, count_of_courses: category.count_of_courses });
  });

  // Count direct courses
  courses.data.forEach((course) => {
    if (categoryMap.has(course.category_id)) {
      categoryMap.get(course.category_id).count_of_courses++;
    }
  });

  // Propagate counts to parent categories
  categories.data.forEach((category) => {
    let current = category;
    while (current.parent && categoryMap.has(current.parent)) {
      categoryMap.get(current.parent).count_of_courses += current.count_of_courses;
      current = categoryMap.get(current.parent);
    }
  });

  return Array.from(categoryMap.values());
}

// Render Categories
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

function renderCategoryTree(categories, depth = 0) {
  return categories.map(category => `
      <li onclick="filterCourses('${category.id}')" style="padding-left: ${depth * 20}px">
          ${category.name} (${category.count_of_courses})
      </li>
      ${category.children.length ? renderCategoryTree(category.children, depth + 1) : ''}
  `).join('');
}

function renderCategories() {
  let tree = buildCategoryTree(allCategories);
  categoryList.innerHTML = `
      <li onclick="filterCourses(null)">All Categories</li>
      ${renderCategoryTree(tree)}
  `;
}

// Render Courses
function renderCourses() {
  if (allCourses.length === 0) {
    courseList.innerHTML = '<p>No courses available.</p>';
    return;
  }

  const isDesktop = window.matchMedia('(min-width: 768px)').matches;

  courseList.innerHTML = allCourses.data
    .map(
      (course) => {
        const category = allCategories.find((cat) => cat.id === course.category_id);
        const mainCategoryName = course ? course.main_category_name : 'Unknown Category';

        const truncatedTitle = isDesktop
          ? truncateText(course.title, 50)
          : course.title;
        const truncatedDescription = isDesktop
          ? truncateText(course.description, 100)
          : course.description;

        return `
          <div class="course-card">
            <img src="${course.image_preview}" alt="${course.title}" />
            <h3>${truncatedTitle}</h3>
            <p>${truncatedDescription}</p>
            <small>Main Category: ${mainCategoryName}</small>
          </div>
        `;
      }
    )
    .join('');
}

// Filter Courses by Category
function filterCourses(categoryId) {
  fetchCourses(categoryId);
}

// Initialize App
function initApp() {
  fetchCourses(); // Fetch all courses initially
}

// Start the Application
initApp();