
const cm = 72 / 2.54;
var orix = 0, oriy = 0;
var font;

setOrigin = (x, y) => {
    //page.moveTo(x * cm, page.getHeight() - y * cm);
    orix = x * cm;
    oriy = y * cm;
    if (page.getHeight() < y * cm)
        oriy = page.getHeight();
}

getOrigin = () => {
    return {x:orix, y:oriy };
}

moveDown = (h) => {
    //page.moveDown(h * cm);
    oriy -= h * cm;
}

moveUp = (h) => {
    //page.moveDown(h * cm);
    oriy += h * cm;
}

moveLeft = (h) => {
    //page.moveDown(h * cm);
    orix -= h * cm;
}

moveRight = (h) => {
    //page.moveDown(h * cm);
    orix += h * cm;
}

drawLines = (s, e, color, thick) => {
    page.drawLine({
        start: {
            x: s.x,
            y: s.y
        },
        end: {
            x: e.x,
            y: e.y
        },
        color: color, // 선 색상 설정 (RGB)
        thickness: thick, // 선 두께 설정
    });
}

drawTexts = (x, y, fontSize, color, text) => {
    page.drawText(text, {
        x: x,
        y: y,
        size: fontSize,
        // font: timesRomanFont,
        color: color,
    })
}
drawRTexts = (x, y, fontSize, color, text) => {
    page.drawText(text, {
        x: orix + x * cm,
        y: oriy + y * cm,
        size: fontSize,
        // font: timesRomanFont,
        color: color,
    })
}

drawBox = (sx, sy, w, h, color) => {
    page.drawRectangle({
        x: sx * cm,
        y: sy * cm,
        width: w * cm,
        height: h * cm,
        color: color,
        borderColor: rgb(1, 0, 0),
        borderWidth: 1.5,
    })
}

drawTextBox = (sx, sy, w, h, color, text, fontSize, fcolor, align = "left") => {

    if (align == "left") {
        tx = sx * cm + fontSize / 3;
        ty = sy * cm + fontSize / 3;
    }
    else if (align == "center") {
        tx = sx * cm + (w / 2);
        ty = sy * cm + h / 2 - fontSize / 3;
    }

    page.drawRectangle({
        x: sx * cm,
        y: sy * cm,
        width: w * cm,
        height: h * cm,
        color: color,
        borderColor: rgb(0, 0, 0),
        borderWidth: 1.0,
    });
    page.drawText(text, {
        x: tx,
        y: ty,
        size: fontSize,
        // font: timesRomanFont,
        color: fcolor
    })
}
drawRTextBox = (sx, sy, w, h, color, text, fontSize, fcolor, align = "left") => {

    const th = font.heightAtSize(fontSize);
    const tw = font.widthOfTextAtSize(text, fontSize);

    if (align == "left") {
        tx = orix + sx * cm + fontSize / 3;
        ty = oriy + sy * cm + fontSize / 1.0;
    }
    else if (align == "center") {
        //tx = orix + sx * cm + (w * cm / 2);
        tx = orix + sx * cm + (w * cm / 2) -  tw  / 2;
        ty = oriy + sy * cm + (h * cm / 2) - fontSize / 3;
    }
    else if (align == "right")
    {
        tx = orix + sx * cm + (w * cm ) -  tw ;
        ty = oriy + sy * cm + (h * cm / 2) - fontSize / 3;
    }

    page.drawRectangle({
        x: orix + sx * cm,
        y: oriy + sy * cm,
        width: w * cm,
        height: h * cm,
        color: color,
        borderColor: rgb(0, 0, 0),
        borderWidth: 0.5,
    });
    page.drawText(text, {
        x: tx,
        y: ty,
        size: fontSize,
        // font: timesRomanFont,
        color: fcolor
    })
}

drawRTextBox1 = (sx, sy, w, h, color, text, fontSize, fcolor, align = "left") => {

    const th = font.heightAtSize(fontSize);
    const tw = font.widthOfTextAtSize(text, fontSize);

    if (align == "left") {
        tx = orix + sx * cm + fontSize / 3;
        ty = oriy + sy * cm + fontSize / 1.0;
    }
    else if (align == "center") {
        tx = orix + sx * cm + (w * cm / 2) -  tw  / 2;
        ty = oriy + sy * cm + (h * cm / 2) - fontSize / 3;
        //ty = oriy + sy * cm + (h * cm / 2) - th  / 2;
    }
    else if (align == "right")
    {
        tx = orix + sx * cm + (w * cm ) -  tw ;
        ty = oriy + sy * cm + (h * cm / 2) - fontSize / 3;
    }

    page.drawRectangle({
        x: orix + sx * cm,
        y: oriy + sy * cm,
        width: w * cm,
        height: h * cm,
        color: color,
        borderColor: rgb(0, 0, 0),
        borderWidth: 0.5,
    });
    page.drawText(text, {
        x: tx,
        y: ty,
        size: fontSize,
        // font: timesRomanFont,
        color: fcolor
    })
}