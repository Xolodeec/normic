$(document).ready(function (){

    $('.resizable').resizable({
        alsoResize: "#mirror",
        cancel: ".cancel",
        minHeight: 50,
        maxHeight: 50,
        grid:[3, 0],
        stop: function (event, ui){

            if(!isPermissionResizable(ui))
            {
                ui.element.width(ui.originalSize.width);

                return false;
            }

            return console.log('Можно расширить');

            /*let listCoordinates = getListFilteredElementsCoordinate(ui);

            let startX = ui.element.offset().left;
            let endX = ui.element.width() + startX;

            console.log(startX);
            console.log(endX);
            console.log(listCoordinates);*/
            // console.log(newListCoordinates);

            /*let widthOneColumn = $('.body-calendar > div:first-child').innerWidth();
            let numberColumn = Math.floor(ui.element.innerWidth() / widthOneColumn);

            let widthOneMinute = widthOneColumn / 60;

            let countMinute = 0;

            if(numberColumn > 0)
            {
                countMinute = Math.floor((ui.element.innerWidth() - (widthOneColumn * numberColumn)) / widthOneMinute);
            }
            else
            {
                countMinute = Math.floor(ui.element.innerWidth() / widthOneMinute);
            }

            let column = $('.header-calendar > div');
            let countHours = $(column[numberColumn]).attr('aria-label');

            console.log(ui);*/
        }
    });

    $(".selector").draggable({
        containment: '.body-calendar',
        grid: [3, 50],
        opacity: 0.5,
        snap: true,
        snapTolerance: 10,
        zIndex: 50,
        stop: function (event, ui)
        {
            if(!isPermissionDraggable(ui))
            {
                ui.helper.css('top', ui.originalPosition.top);
                ui.helper.css('left', ui.originalPosition.left);

                return false;
            }

            return $('.modal-draggable').modal('show');;
        }
    });
});


function getListDraggableElement(ui)
{
    let listCoordinates = {};
    let number = 0;

    let row = $('.body-calendar > div').filter(function(index){
        return $(this).offset().top + 1 == ui.offset.top
    });

    let leftBlock = {
        left: row.find('> div:first-child').offset().left,
        top: row.find('> div:first-child').offset().top,
        width: 0,
    };

    let rightBlock = {
        left: row.find('> div:last-child').offset().left + row.find('> div:last-child').width(),
        top: row.find('> div:last-child').offset().top,
        width: 0,
    };

    row.find('.ui-draggable').each(function (index){
        let offset = $(this).offset();

        if(offset.left > leftBlock.left && ui.offset.left > offset.left)
        {
            leftBlock.left = offset.left;
            leftBlock.top = offset.top;
            leftBlock.width = $(this).width();
        }

        if(offset.left < rightBlock.left && ui.offset.left < offset.left)
        {
            rightBlock.left = offset.left;
            rightBlock.top = offset.top;
            rightBlock.width = $(this).width();
        }
    });

    return {
        leftBlock: leftBlock,
        rightBlock: rightBlock,
    };
}

function getListResizableElement(ui)
{
    let parentRow = ui.element.closest('.row');
    let listCoordinateElements = {};

    parentRow.find('.ui-resizable').each(function (index)
    {
        let offset = $(this).offset();

        if(offset.left !== ui.element.offset().left)
        {
            listCoordinateElements[index] = offset;
        }
    });

    return listCoordinateElements;
}

function isPermissionResizable(ui)
{
    let isPermissionResizable = true;

    let startX = ui.element.offset().left;
    let endX = ui.element.width() + startX;

    let listElementCoordinates = getListResizableElement(ui);

    $.each(listElementCoordinates, function (index, data){
        if(endX >= data.left)
        {
            isPermissionResizable = false;
        }
    });

    return isPermissionResizable;
}

function isPermissionDraggable(ui)
{
    let nearestElement = getListDraggableElement(ui);

    if(nearestElement.leftBlock.left + nearestElement.leftBlock.width + 1 < ui.offset.left && ui.offset.left + ui.helper.width() < nearestElement.rightBlock.left)
    {
        return true;
    }

    return false;
}
