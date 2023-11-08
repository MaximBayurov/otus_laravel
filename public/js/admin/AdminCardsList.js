class AdminCardsList {
    constructor($cardsList, cardSelector) {
        this.$cardsList = $cardsList;
        this.cardSelector = cardSelector;
        this.nextCardIndex = $cardsList.find(cardSelector).length;
    }

    /**
     * Меняет аттрибуты переданной карточки
     * @param $card
     */
    changeCardAttributes($card) {
        $card.find('select,textarea').each((i, item) => {
            let $item = $(item);
            let $label = $item.parent().find('label');
            $item.val(null);
            let $newId = $item.attr('id').replace(/\d*$/, this.nextCardIndex)
            $item.attr('id', $newId)
            $label.attr('for', $newId)
            $item.attr('name', $item.attr('name').replace(/\[\d*\]/, `[${this.nextCardIndex}]`))
            $item.removeClass('is-invalid');
            $item.find('.invalid-feedback')?.remove();
        })
    }

    /**
     * Обработчик события при нажатии на кнопку добавления карточки
     *
     * @param event
     */
    onAddCardButtonClick(event) {
        let $lastCard = this.$cardsList.find(this.cardSelector).last();
        let $clone = $lastCard.clone();

        this.changeCardAttributes($clone);

        $clone.appendTo(this.$cardsList)
        this.nextCardIndex++;
    }

    /**
     * Обработчик события при нажатии на кнопку удаления карточки
     *
     * @param event
     */
    onRemoveCardButtonClick(event) {
        let $card = $(event.target).closest(this.cardSelector);
        if(this.$cardsList.find(this.cardSelector).length > 1) {
            $(event.target).closest('[data-card]').remove();
            return;
        }
        this.nextCardIndex = 0;
        this.changeCardAttributes($card);
    }
}
