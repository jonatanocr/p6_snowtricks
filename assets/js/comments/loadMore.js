
export default function() {
    return ``;
};

document.addEventListener("DOMContentLoaded", function () {
    const $ = require('jquery');
    var buttonComments = document.getElementById("loadMoreCommentsBtn");
    var min = 2;
    var trickId = document.getElementById('trickId').value;
    buttonComments.addEventListener("click", function (event) {
        $.ajax({
            url: "/trick/" + trickId,
            type: "POST",
            dataType: "json",
            data: {
                "comment_min": min
            },
            async: true,
            success: function (data)
            {
                var moreCommentsData = JSON.parse(data);
                if (moreCommentsData.length > 0) {
                    moreCommentsData.forEach((element) => {
                        function dateChange(d) {
                            var d2 = new Date(d);
                            return d2.getDate() + '/' + (d2.getMonth()+1) + '/' + d2.getFullYear() + ' ' + d2.getHours() + ':' + d2.getMinutes() + ':' + d2.getSeconds()
                        }
                        var commentBlock = document.getElementById('comment_block');
                        var moreCommentHead = document.createElement("p");
                        //moreCommentHead.innerText = element.created_date.date + ' ' + element.author.username;
                        moreCommentHead.innerText = '[' + dateChange(element.created_date.date) + '] ' + element.author.username;
                        commentBlock.appendChild(moreCommentHead);
                        var moreCommentContent = document.createElement("p");
                        moreCommentContent.innerText = element.content;
                        commentBlock.appendChild(moreCommentContent);
                    } )
                    min+= 2;
                } else {
                    buttonComments.style.display = "none";
                }
            }
        });
        return false;
    });

/*
    button.addEventListener("click", function (event) {

        console.log('works')
    });
     */
});