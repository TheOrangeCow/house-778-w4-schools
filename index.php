<?php
include "../base/chech.php"; 
include "../base/chech2.php"; 
include "../base/main.php";
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="https://house-778.theorangecow.org/base/style.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">
        <link rel="icon" href="https://house-778.theorangecow.org/base/icon.ico" type="image/x-icon">
        <title>W4 schools</title>
    </head>
    <body>
        <canvas class="back" id="canvas"></canvas>
        <?php include '../base/sidebar.php'; ?>
        <div class="con">
            <button class="circle-btn" onclick="openNav()">☰</button>  
            <h1>W4 schools</h1>
            <a href="submit.php" class="submit-link">Submit a New w4</a>
            <div id="categories-container"></div>
            <div id="tutorials-container"></div>
            <script>
                fetch('dater.php')
                    .then(response => response.json())
                    .then(data => {
                        const categoriesContainer = document.getElementById('categories-container');
                        const tutorialsContainer = document.getElementById('tutorials-container');
                        const categories = new Set(data.categories);
                
                        categories.forEach(category => {
                            const categoryElement = document.createElement('div');
                            categoryElement.className = 'category';
                            categoryElement.textContent = category;
                            categoryElement.dataset.category = category;
                            categoriesContainer.appendChild(categoryElement);
                        });
                
                        const allCategoryElement = document.createElement('div');
                        allCategoryElement.className = 'category active';
                        allCategoryElement.textContent = 'All';
                        allCategoryElement.dataset.category = 'All';
                        categoriesContainer.insertBefore(allCategoryElement, categoriesContainer.firstChild);
                
                        categoriesContainer.addEventListener('click', function(event) {
                            if (event.target.classList.contains('category')) {
                                document.querySelectorAll('.category').forEach(el => el.classList.remove('active'));
                                event.target.classList.add('active');
                                const selectedCategory = event.target.dataset.category;
                                displayTutorials(selectedCategory);
                            }
                        });
                
                        function displayTutorials(category) {
                            tutorialsContainer.innerHTML = '';
                            const filteredTutorials = category === 'All'
                                ? data.tutorials
                                : data.tutorials.filter(tutorial => tutorial.category === category);
                
                            filteredTutorials.forEach(tutorial => {
                                const tutorialElement = document.createElement('div');
                                tutorialElement.className = 'tutorial';
                                tutorialElement.innerHTML = `
                                    <h2><a href="tutorial.php?tutorial=${tutorial.id}">${tutorial.title}</a></h2>
                                    <p>${tutorial.description}</p>
                                `;
                                tutorialsContainer.appendChild(tutorialElement);
                            });
                        }
                
                        displayTutorials('All');
                    })
                    .catch(error => console.error('Error loading tutorials:', error));
            </script>
        </div>
    </body>
    <script src="https://theme.house-778.theorangecow.org/background.js"></script>
    <script src="https://house-778.theorangecow.org/base/main.js"></script>
    <script src="https://house-778.theorangecow.org/base/sidebar.js"></script>
</html>
