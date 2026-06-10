var newsArticles = new Array();

newsArticles[0] = {
  title: "Final Exam Schedule Released",
  date: "April 28, 2026",
  category: "Notice",
  summary: "The latest final examination schedule has been released for all students.",
  content: "The Academic Affairs Department has released the updated final examination schedule. Students are advised to check the timetable carefully and prepare according to their examination dates.",
  image: "exam.jpg"
};

newsArticles[1] = {
  title: "Campus Sports Day 2026",
  date: "April 26, 2026",
  category: "Event",
  summary: "Students are invited to join the annual campus sports day.",
  content: "InfoHub is announcing the Campus Sports Day 2026. Students can participate in football, badminton, futsal, relay race and other activities.",
  image: "sports.jpg"
};

newsArticles[2] = {
  title: "Iran dakwa 2 peluru berpandu kena kapal perang AS",
  date: "April 24, 2026",
  category: "News",
  summary: "Iran dakwa 2 peluru berpandu kena kapal perang AS",
  content: "DUBAI: Tentera laut Iran menghalang kapal perang Amerika Syarikat (AS)...",
  image: "iran.jpg"
};

var newsList = document.getElementById("newsList");

function renderNews(data) {
  newsList.innerHTML = "";

  var i;

  for (i = 0; i < data.length; i++) {

    newsList.innerHTML +=
  "<div class='news-card'>" +

    "<div class='news-image'>" +
      "<img src='" + data[i].image + "'>" +
    "</div>" +

    "<div class='news-content'>" +
      "<div class='news-meta'>" + data[i].date + " · " + data[i].category + "</div>" +
      "<div class='news-title'>" + data[i].title + "</div>" +
      "<p>" + data[i].summary + "</p>" +
      "<a href='article.html?id=" + i + "'>Read More →</a>" +
    "</div>" +

  "</div>";

  }
}

renderNews(newsArticles);