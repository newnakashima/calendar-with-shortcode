const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
const today = new Date();
let currentYear = today.getFullYear();
let currentMonth = today.getMonth() + 1;

const lastmonth = document.querySelector('.cws-lastmonth');
const thismonth = document.querySelector('.cws-thismonth');
const yearpart = document.querySelector('.cws-yearpart');
const monthpart = document.querySelector('.cws-monthpart');
const nextmonth = document.querySelector('.cws-nextmonth');
let monthCursor = {
    prevMonthYear: function (date) {
        return date.getMonth() === 0 ? date.getFullYear() - 1 : date.getFullYear();
    },
    nextMonthYear: function (date) {
        return date.getMonth() === 11 ? date.getFullYear() + 1 : date.getFullYear();
    },
    prevMonth: function (date) {
        return date.getMonth() === 0 ? 12 : date.getMonth();
    },
    nextMonth: function (date) {
        return date.getMonth() === 11 ? 1 : date.getMonth() + 2;
    }
};

lastmonth.addEventListener('click', e => {
    let current = new Date(currentYear, currentMonth - 1, 1);
    currentMonth = monthCursor.prevMonth(current);
    currentYear = monthCursor.prevMonthYear(current);
    createCalendar(new Date(currentYear, currentMonth - 1, 1));
});
nextmonth.addEventListener('click', e => {
    let current = new Date(currentYear, currentMonth - 1, 1);
    currentMonth = monthCursor.nextMonth(current);
    currentYear = monthCursor.nextMonthYear(current);
    createCalendar(new Date(currentYear, currentMonth - 1, 1));
});

// データ形式サンプル
let events = [
    {
        year: 2018,
        month: 2,
        date: 20,
        title: 'test',
        description: 'test description',
        href: 'https://www.google.co.jp'
    }
];

// console.log(json_events);
// 
// events = json_events.map(e => {
//     let ev_date = new Date(e.cws_event_date);
//     return {
//         year: ev_date.getFullYear(),
//         month: ev_date.getMonth() + 1,
//         date: ev_date.getDate(),
//         title: e.post_title,
//         description: e.post_content,
//         href: e.guid
//     };
// });

function clearCalendar () {
    const calendar = document.querySelectorAll('.cws-day');
    calendar.forEach(e => e.parentNode.removeChild(e));
}

function pad(string, width, char) {
    var padChar = char !== undefined ? char : '0';
    var padStr = "";
    for (var i=0,len=width-String(string).length; i<len; i++) {
        padStr += padChar;
    }
    return padStr+String(string);
}

function createCalendar (date) {
    clearCalendar();
    const year = date.getFullYear();
    const month = date.getMonth() + 1;
    const weekday = weekdays[date.getDay()];
    const firstDay = new Date(year, month - 1, 1);
    const lastMonth = new Date(year, month - 1, 0);
    const endDay = new Date(year, month, 0);
    const thisMonthLength = endDay.getDate();

    const calendar_header = document.querySelector('.cws-calendar-header');
    lastmonth.innerHTML = monthCursor.prevMonth(date) + '月';
    yearpart.innerText = year + '年';
    monthpart.innerText = month + '月';
    nextmonth.innerHTML = monthCursor.nextMonth(date) + '月';
    const calendar = document.querySelector('div.cws-calendar-container');
    for (let i = 1; i < firstDay.getDay() + 1; i++) {
        let day = document.createElement('div');
        day.classList.add('cws-item');
        day.classList.add('cws-day');
        day.classList.add('cws-last-month');
        let datenum = i + (lastMonth.getDate() - firstDay.getDay());
        let id = 'cws-' + pad(monthCursor.prevMonthYear(date), 4) + pad(monthCursor.prevMonth(date), 2) + pad(datenum, 2);
        day.id = id;
        day.innerHTML = '<a href="#' + id + '" class="cws-day-number">' + datenum + '</a>';
        calendar.appendChild(day);
    }
    for (let i = 1; i <= thisMonthLength; i++) {
        let day = document.createElement('div');
        day.classList.add('cws-item');
        day.classList.add('cws-day');
        let id = 'cws-' + pad(currentYear, 4) + pad(currentMonth, 2) + pad(i, 2);
        if (i === today.getDate() && currentMonth === today.getMonth() + 1 && currentYear === today.getFullYear()) {
            day.classList.add('cws-today');
        }
        day.id = id;
        day.innerHTML = '<a href="#' + id + '" class="cws-day-number">' + i + '</a>';
        calendar.appendChild(day);
    }
    for (let i = endDay.getDay() + 1; i < weekdays.length; i++) {
        let day = document.createElement('div');
        day.classList.add('cws-item');
        day.classList.add('cws-day');
        day.classList.add('cws-next-month');
        day.innerHTML = '<a class="cws-day-number">' + i + '</a>';
        calendar.appendChild(day);
    }
    ajaxRequest('/wp-json/wp/v2/events', (res) => {
        let json = JSON.parse(res);
        console.log(json);
        createEvents(json);
    });
}

function modalShow(description) {
    let modal = document.querySelector('.cws-modal');
    modal.style.display = 'block';
    let content = document.querySelector('.cws-modal-container');
    content.innerHTML = description;
    let bg = document.querySelector('.cws-modal-bg');
    bg.style.display = 'block';
}
function modalClose() {
    let modal = document.querySelector('.cws-modal');
    modal.style.display = 'none';
    let bg = document.querySelector('.cws-modal-bg');
    bg.style.display = 'none';
}
function setEvents(data) {
    data.forEach(d => {
        let target = document.querySelector('#cws-' + pad(d.year, 4) + pad(d.month, 2) + pad(d.date, 2));
        if (target === null) return;
        let eventdom = document.createElement('div');
        eventdom.innerText = d.title;
        eventdom.addEventListener('click', () => {
            modalShow(d.description);
        });
        target.appendChild(eventdom);
    });
}
document.querySelector('.cws-modal-close a').addEventListener('click', () => {
    modalClose();
});

function ajaxRequest(url, callback, method = 'GET') {
    let xhr = new XMLHttpRequest();
    xhr.open(method, url);
    xhr.onload = function (e) {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                callback(xhr.responseText);
            } else {
                console.error(xhr.statusText);
            }
        }
    };
    xhr.onerror = function (e) {
        console.error(xhr.statusText);
    };
    xhr.send(null);
}

function createEvents(json) {
    let mapped = json.map(e => {
        let ev_date = new Date(e.event_meta.cws_event_date);
        console.log(ev_date);
        return {
            year: ev_date.getFullYear(),
            month: ev_date.getMonth() + 1,
            date: ev_date.getDate(),
            title: e.title.rendered,
            description: e.content.rendered,
            href: e.link
        };
    });
    setEvents(mapped);
}

createCalendar(today);

