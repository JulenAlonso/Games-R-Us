const data = [
    {
        place: "Hyrule",
        title1: "ZELDA:",
        title2: "TEARS OF THE KINGDOM",
        description:
        "Une fuerzas con héroes legendarios para salvar Hyrule en esta mágica continuación de la saga de Zelda.",        
            image: "https://images.alphacoders.com/127/thumb-1920-1272163.jpg",
    },
    {
        place: "Football",
        title2: "FC 25",
        description:
            "Forma equipo con tus colegas en tus modos favoritos con el nuevo Rush de 5 contra 5, compite contra Mbappé, Lewandowski, Griezman, Nico Williams, Lamine, Kubo, Aitana, Weir, Ludmila y disfruta de la emoción hasta el final.",
        image: "https://cdn.hk01.com/di/media/images/dw/20240717/890244248866131968615403.jpeg/wjoHC2jhhGrNOscfiW6_5nYnmUvHtqssA_tRIAP7USA?v=w1920",
    },
    {
        place: "Ciencia Ficción",
        title2: "ALIEN BREED",
        description:
            "Alien Breed es un videojuego run and gun visto desde arriba lanzado por Team17 para Amiga en 1991, y después por MicroLeague para MS-DOS en 1993. El juego es el primero en la serie Alien Breed.",
        image: "https://m.media-amazon.com/images/I/81gNfm9HTmL.png",
    },
    {
        place: "Night City",
        title2: "CYBERPUNK 2077",
        description:
        "Explora un mundo abierto en el futuro distópico de Night City, donde cada decisión importa y el peligro acecha en cada esquina.",        
            image: "https://i.blogs.es/b109e9/cyberpunk2077-johnny-v-rgb_en/1366_2000.jpg",
    },
    {
        place: "Lands Between",
        title2: "ELDEN RING",
        description:
        "Embárcate en una épica aventura en un mundo lleno de misterios, desafíos y criaturas fantásticas, creado por FromSoftware.",       
            image: "https://wallpapers.com/images/featured/elden-ring-6r85th0gnhifsqd0.jpg",
    },
    {
        place: "Paldea Region",
        title2: "POKÉMON ESCARLATA & PURPURA",
        description:
        "Atrapa, entrena y explora en una nueva región con mecánicas innovadoras y batallas emocionantes.",        
            image: "https://images3.alphacoders.com/128/thumb-1920-1289473.png",
    },
];

const _ = (id) => document.getElementById(id);
const cards = data
    .map(
        (i, index) =>
            `<div class="card" id="card${index}" style="background-image:url(${i.image})"  ></div>`
    )
    .join("");

const cardContents = data
    .map(
        (i, index) => `<div class="card-content" id="card-content-${index}">
<div class="content-start"></div>
<div class="content-place">${i.place}</div>

<div class="content-title-2">${i.title2}</div>

</div>`
    )
    .join("");

// <div class="content-title-1">${i.title}</div>
const sildeNumbers = data
    .map(
        (_, index) =>
            `<div class="item" id="slide-item-${index}" >${index + 1}</div>`
    )
    .join("");
_("demo").innerHTML = cards + cardContents;
_("slide-numbers").innerHTML = sildeNumbers;

const range = (n) =>
    Array(n)
        .fill(0)
        .map((i, j) => i + j);
const set = gsap.set;

function getCard(index) {
    return `#card${index}`;
}
function getCardContent(index) {
    return `#card-content-${index}`;
}
function getSliderItem(index) {
    return `#slide-item-${index}`;
}

function animate(target, duration, properties) {
    return new Promise((resolve) => {
        gsap.to(target, {
            ...properties,
            duration: duration,
            onComplete: resolve,
        });
    });
}

let order = [0, 1, 2, 3, 4, 5];
let detailsEven = true;

let offsetTop = 200;
let offsetLeft = 700;
let cardWidth = 200;
let cardHeight = 300;
let gap = 40;
let numberSize = 50;
const ease = "sine.inOut";

function init() {
    const [active, ...rest] = order;
    const detailsActive = detailsEven ? "#details-even" : "#details-odd";
    const detailsInactive = detailsEven ? "#details-odd" : "#details-even";
    const { innerHeight: height, innerWidth: width } = window;
    offsetTop = height - 430;
    offsetLeft = width - 830;

    gsap.set("#pagination", {
        top: offsetTop + 330,
        left: offsetLeft,
        y: 200,
        opacity: 0,
        zIndex: 60,
    });
    gsap.set("nav", { y: -200, opacity: 0 });

    gsap.set(getCard(active), {
        x: 0,
        y: 0,
        width: window.innerWidth,
        height: window.innerHeight,
    });
    gsap.set(getCardContent(active), { x: 0, y: 0, opacity: 0 });
    gsap.set(detailsActive, { opacity: 0, zIndex: 22, x: -200 });
    gsap.set(detailsInactive, { opacity: 0, zIndex: 12 });
    gsap.set(`${detailsInactive} .text`, { y: 100 });
    // gsap.set(`${detailsInactive} .title-1`, { y: 100 });
    gsap.set(`${detailsInactive} .title-2`, { y: 100 });
    gsap.set(`${detailsInactive} .desc`, { y: 50 });
    gsap.set(`${detailsInactive} .cta`, { y: 60 });

    gsap.set(".progress-sub-foreground", {
        width: 500 * (1 / order.length) * (active + 1),
    });

    rest.forEach((i, index) => {
        gsap.set(getCard(i), {
            x: offsetLeft + 400 + index * (cardWidth + gap),
            y: offsetTop,
            width: cardWidth,
            height: cardHeight,
            zIndex: 30,
            borderRadius: 10,
        });
        gsap.set(getCardContent(i), {
            x: offsetLeft + 400 + index * (cardWidth + gap),
            zIndex: 40,
            y: offsetTop + cardHeight - 100,
        });
        gsap.set(getSliderItem(i), { x: (index + 1) * numberSize });
    });

    gsap.set(".indicator", { x: -window.innerWidth });

    const startDelay = 0.6;

    gsap.to(".cover", {
        x: width + 400,
        delay: 0.5,
        ease,
        onComplete: () => {
            setTimeout(() => {
                loop();
            }, 500);
        },
    });
    rest.forEach((i, index) => {
        gsap.to(getCard(i), {
            x: offsetLeft + index * (cardWidth + gap),
            zIndex: 30,
            delay: 0.05 * index,
            ease,
            delay: startDelay,
        });
        gsap.to(getCardContent(i), {
            x: offsetLeft + index * (cardWidth + gap),
            zIndex: 40,
            delay: 0.05 * index,
            ease,
            delay: startDelay,
        });
    });
    gsap.to("#pagination", { y: 0, opacity: 1, ease, delay: startDelay });
    gsap.to("nav", { y: 0, opacity: 1, ease, delay: startDelay });
    gsap.to(detailsActive, { opacity: 1, x: 0, ease, delay: startDelay });
}

let clicks = 0;

function step() {
    return new Promise((resolve) => {
        order.push(order.shift());
        detailsEven = !detailsEven;

        const detailsActive = detailsEven ? "#details-even" : "#details-odd";
        const detailsInactive = detailsEven ? "#details-odd" : "#details-even";

        document.querySelector(`${detailsActive} .place-box .text`).textContent =
            data[order[0]].place;
        // document.querySelector(`${detailsActive} .title-1`).textContent =
        // data[order[0]].title;
        document.querySelector(`${detailsActive} .title-2`).textContent =
            data[order[0]].title2;
        document.querySelector(`${detailsActive} .desc`).textContent =
            data[order[0]].description;

        gsap.set(detailsActive, { zIndex: 22 });
        gsap.to(detailsActive, { opacity: 1, delay: 0.4, ease });
        gsap.to(`${detailsActive} .text`, {
            y: 0,
            delay: 0.1,
            duration: 0.7,
            ease,
        });
        // gsap.to(`${detailsActive} .title-1`, {
        // y: 0,
        // delay: 0.15,
        // duration: 0.7,
        // ease,
        // });
        gsap.to(`${detailsActive} .title-2`, {
            y: 0,
            delay: 0.15,
            duration: 0.7,
            ease,
        });
        gsap.to(`${detailsActive} .desc`, {
            y: 0,
            delay: 0.3,
            duration: 0.4,
            ease,
        });
        gsap.to(`${detailsActive} .cta`, {
            y: 0,
            delay: 0.35,
            duration: 0.4,
            onComplete: resolve,
            ease,
        });
        gsap.set(detailsInactive, { zIndex: 12 });

        const [active, ...rest] = order;
        const prv = rest[rest.length - 1];

        gsap.set(getCard(prv), { zIndex: 10 });
        gsap.set(getCard(active), { zIndex: 20 });
        gsap.to(getCard(prv), { scale: 1.5, ease });

        gsap.to(getCardContent(active), {
            y: offsetTop + cardHeight - 10,
            opacity: 0,
            duration: 0.3,
            ease,
        });
        gsap.to(getSliderItem(active), { x: 0, ease });
        gsap.to(getSliderItem(prv), { x: -numberSize, ease });
        gsap.to(".progress-sub-foreground", {
            width: 500 * (1 / order.length) * (active + 1),
            ease,
        });

        gsap.to(getCard(active), {
            x: 0,
            y: 0,
            ease,
            width: window.innerWidth,
            height: window.innerHeight,
            borderRadius: 0,
            onComplete: () => {
                const xNew = offsetLeft + (rest.length - 1) * (cardWidth + gap);
                gsap.set(getCard(prv), {
                    x: xNew,
                    y: offsetTop,
                    width: cardWidth,
                    height: cardHeight,
                    zIndex: 30,
                    borderRadius: 10,
                    scale: 1,
                });

                gsap.set(getCardContent(prv), {
                    x: xNew,
                    y: offsetTop + cardHeight - 100,
                    opacity: 1,
                    zIndex: 40,
                });
                gsap.set(getSliderItem(prv), { x: rest.length * numberSize });

                gsap.set(detailsInactive, { opacity: 0 });
                gsap.set(`${detailsInactive} .text`, { y: 100 });
                // gsap.set(`${detailsInactive} .title-1`, { y: 100 });
                gsap.set(`${detailsInactive} .title-2`, { y: 100 });
                gsap.set(`${detailsInactive} .desc`, { y: 50 });
                gsap.set(`${detailsInactive} .cta`, { y: 60 });
                clicks -= 1;
                if (clicks > 0) {
                    step();
                }
            },
        });

        rest.forEach((i, index) => {
            if (i !== prv) {
                const xNew = offsetLeft + index * (cardWidth + gap);
                gsap.set(getCard(i), { zIndex: 30 });
                gsap.to(getCard(i), {
                    x: xNew,
                    y: offsetTop,
                    width: cardWidth,
                    height: cardHeight,
                    ease,
                    delay: 0.1 * (index + 1),
                });

                gsap.to(getCardContent(i), {
                    x: xNew,
                    y: offsetTop + cardHeight - 100,
                    opacity: 1,
                    zIndex: 40,
                    ease,
                    delay: 0.1 * (index + 1),
                });
                gsap.to(getSliderItem(i), { x: (index + 1) * numberSize, ease });
            }
        });
    });
}

async function loop() {
    await animate(".indicator", 2, { x: 0 });
    await animate(".indicator", 0.8, { x: window.innerWidth, delay: 0.3 });
    set(".indicator", { x: -window.innerWidth });
    await step();
    loop();
}

async function loadImage(src) {
    return new Promise((resolve, reject) => {
        let img = new Image();
        img.onload = () => resolve(img);
        img.onerror = reject;
        img.src = src;
    });
}

async function loadImages() {
    const promises = data.map(({ image }) => loadImage(image));
    return Promise.all(promises);
}

async function start() {
    try {
        await loadImages();
        init();
    } catch (error) {
        console.error("One or more images failed to load", error);
    }
}

start();
