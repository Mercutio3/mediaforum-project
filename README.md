# "MedRev"
## Media Review Forum

Santiago Dominguez Ham
Web App Development - Constructor University

## Requirements

- XAMPP, MAMP, or another PHP/MySQL server
- A web browser, preferably Google Chrome or one analogous to Chrome

## Setup

1. Start your local Apache / MySQL server
2. Place project files in htdocs folder (or equivalent)
3. Open PHPMyAdmin (http://localhost:8888/phpMyAdmin5/index.php)
4. Create a new database
5. Use provided .sql file for importing
6. Update PHP credentials in php/config.php to fit your installation
7. Access site in your browser (http://localhost:8888/index.html)

## Phase 3 - JavaScript + Backend

JavaScript files are now contained in the /javascript folder. These give
functionality to the website and allow content to be loaded dynamically.

Some of the HTML files in the root folder have been converted to .php files.
This is the case for pages that need to retreive and display information
from the SQL database. More PHP files are found in the /php folder. These
assist the javascript files with functionality and connect to the database.
In short, data persistance has been achieved.

The home page was updated with a hero section and scrolling featured media
feature. The whole project is now called "MedRev". Not a very creative title.

Some users have already been added to the database, primarily for testing
purposes. The user "MysteryDude" is set to a private profile, to demonstrate
this feature.

Keep in mind, being logged in is necessary to access most of MedRev's features.

Currently, eight media types are registered. This number can be arbitrarily
extended with ease.

### GUEST USER CREDENTIALS

Username: Guest
password: guest

### HOW TO VERIFY NEW USER EMAIL

Email verification is not yet fully implemented. To still allow for new
users to register, verification tokens are stored in a .txt file located in 
the php/tokens folder. You then have to enter the verification page manually 
in your browser, using the following URL:

http://localhost:8888/php/verify-email.php?token=TOKEN

Where you replace "TOKEN" with your token.

-------------------------------------------------

## Phase 2.2 - HTML + CSS

CSS elements have been added to the existing HTML pages. The following is
some information about the chosen color scheme, which is also found in the
global.css file:

1. Dark Brown (#5A3E36) for headers, footers, and text on light background.
2. Light Cream (#F5F1E3) for main background and text on dark background.
3. Golden Yellow (#ЕЗB23C) for accents like buttons, links, and highlights.
    3a. A slightly darker yellow is used for hover attributes.
    3b. A slightly lighter yellow is used for active attributes.
4. Muted Gold (#A37C40) as a secondary, less prominent accent color.
5. Warm Gray (#C4B7A8) for netural borders, box-shadows, and dividers.
6. Light Brown (#D4A373) for accent borders, box-shadows, and dividers.

The title of the website is not yet chosen. "Media Review Forum" is a
placeholder.

-------------------------------------------------

## Phase 2 - HTML
This README file contains a description of each HTML file in my project, how
they interact with each other, and what functionality is planned for each of
them for later phases of the project.

Overall, each page is dedicated to one of my project's "main" features, such
as browsing, user profile, review submission, etc. All of the smaller or more
complicated features that have yet to be added should fit well within one of
these pages, instead of getting a completely new section. All pages include a
navigation section at the top for easy browsing and a footer at the bottom.

In order to make it easier to implement CSS and JavaScript in the future, I
have already assigned classes, <div>s, and IDs for most elements in my project so far.
Repeated elements that will be inserted dynamically are contained in <article>
tags for ease of implementation.

All users land on the index.html page (home page). From here, they will either
browsing reviews right away or log in first. A user with no account will
quickly encounter one of the features that are exclusive to logged-in users
(listed below) and will be redirected to either log in or sign up. Once a user
is logged in, a typical pattern would involve checking notifications, posting
a review, and browsing other reviews.

--------------
index.html
This is the homepage. It contains sections for hero, trending reviews, and
featured media. Trending reviews are placeholder data for now but will be those
with the highest "engagement points" (like = 1 point, comment = 2 points).
Featured media are chosen by me for now.

--------------
browse.html
This is the browse page. Reviews will be displayed and can be filtered. The
reviews displayed are placeholder data for now. I am currently still debating
if the search feature should be contained within the Browse page (as it is now)
or given its own page.

--------------
submit.html
This is the submission page. Users will enter their review details and submit.
In the final product only logged-in users can access this page.

--------------
notifications.html
This is the notifications page. Notifications for when your reviews are liked or
commented on will appear here. You will be able to mark them as read or filter
them by type. Notifications displayed are placeholder for now. In the final
product only logged-in users can access this page, and notifications will be
displayed in real-time as they arrive.

--------------
profile.html
This is the profile page. It displays user info, statistics, and reviews posted.
In the final product only logged-in users can access this page. This page is
meant as a "template" for all user profiles, with a user ID stored in the URL.
If a user is viewing their own profile, the "edit profile" button will appear.
Displayed user details are a placeholder for now. This is also where the statistics
will be displayed using charts and graphs.

--------------
account.html
This is the account page. A logged-in user's accont page is only visible to
them. Here they can change their profile info, password, privacy, and delete
their account.

--------------
about.html
This is the about page with info about the project, frequently asked questions,
a contact form, and links to the user register page.

--------------
logout.html
This is the logout page. In the final product, it won't be displayed to the user
at all, as it will likely only consist of a short PHP script to end the session
and effectively log out.

--------------
register.html
This is the signup page. A username or email can only be used once, and it must
follow the format "email@example.example". In the future, this email will be
used for two-factor authentication processes.

--------------
login.html
This is the login page. Login will be necessary to access submit, profile, account
and notifications pages. Trying to access any of the aforementioned pages while
not logged in will redirect you here. You must also be logged in to enable
liking and commenting on reviews.

--------------
review.html
This is the dedicated review page. All the details and comments are displayed.
Only logged-in users can leave comments.