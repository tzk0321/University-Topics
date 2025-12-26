


// collapse 折疊效果
var questions = document.querySelectorAll('.question');
var answers = document.querySelectorAll('.answer');
for (var question of questions) {
    question.onclick = toggle;
}
function toggle(e) {
    var answer = e.target.nextElementSibling;
    if (!answer.style.maxHeight) {
        answer.style.maxHeight = answer.scrollHeight + 'px';
        e.target.classList.toggle('arrow-rotate');
    } else {
        answer.style.maxHeight = '';
        e.target.classList.toggle('arrow-rotate');
    }
}