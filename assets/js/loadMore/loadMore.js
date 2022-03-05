
export default function() {
    return "";
}

document.addEventListener("DOMContentLoaded", function () {
    const $ = require("jquery");
    var pageType = document.getElementById("pageType").value;
    if (pageType === "trick_1") {
        var buttonComments = document.getElementById("loadMoreCommentsBtn");
        var min = 2;
        var trickId = document.getElementById("trickId").value;
        buttonComments.addEventListener("click", function (event) {
            $.ajax({
                url: "/trick/" + trickId,
                type: "POST",
                dataType: "json",
                data: {
                    "comment_min": min
                },
                async: true,
                success: function (data) {
                    var moreCommentsData = JSON.parse(data);
                    if (moreCommentsData.length > 0) {
                        moreCommentsData.forEach((element) => {
                            function dateChange(d) {
                                var d2 = new Date(d);
                                return d2.getDate() + "/" + (d2.getMonth()+1) + "/" + d2.getFullYear() + " " + d2.getHours() + ":" + d2.getMinutes() + ":" + d2.getSeconds();
                            }
                            var commentBlock = document.getElementById("comment_block");
                            var moreCommentHead = document.createElement("p");
                            moreCommentHead.innerText = "[" + dateChange(element.created_date.date) + "] " + element.author.username;
                            commentBlock.appendChild(moreCommentHead);
                            var moreCommentContent = document.createElement("p");
                            moreCommentContent.innerText = element.content;
                            commentBlock.appendChild(moreCommentContent);
                        } );
                        min+= 2;
                    } else {
                        buttonComments.style.display = "none";
                    }
                }
            });
            return false;
        });
    } else if (pageType === "trick_index") {
        var buttonTricks = document.getElementById("loadMoreTricksBtn");
        var min = 4;
        buttonTricks.addEventListener("click", function (event) {
            $.ajax({
                url: "/loadmore",
                type: "POST",
                dataType: "json",
                data: {
                    "trick_min": min
                },
                async: true,
                success: function (data) {
                    var moreTrickRow = document.createElement("div");
                    moreTrickRow.classList.add("row");
                    moreTrickRow.classList.add("justify-content-center");
                    var moreTricksData = JSON.parse(data);
                    if (moreTricksData.length > 0) {
                        moreTricksData.forEach((element) => {
                            moreTrickRow.innerHTML += element;
                            document.getElementById("tricks_block").appendChild(moreTrickRow);
                        })
                    }
                    if (moreTricksData.length < 4) {
                        document.getElementById("loadMoreTricksBtn").style.display = "none";
                    }
                    min+= 4;
                }
            });
        });
    }

});