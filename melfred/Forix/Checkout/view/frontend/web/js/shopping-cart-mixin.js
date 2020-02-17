define(['jquery','underscore','mage/translate'], function($, _){
    return function(originalShoppingCart){
        $.widget(
            'mage.shoppingCart',              //named widget we're redefining
            $['mage']['shoppingCart'], {
                events: {},
                qtyData: {},
                _create: function(){
                    this._super();
                    items = $.find('[data-role="cart-item-qty"]');
                    _.each(items, function(value, index){
                        this._storeQtyAdded(value);
                    },this);
                    this._addQtyInputChangeEvent();
                    this._bind();
                },
                _addQtyInputChangeEvent: function(){
                    var self = this;
                    this.events['keyup ' + 'input[data-role="cart-item-qty"]'] = function (event) {
                        self._showItemUpdateButton($(event.target));
                    };
                },
                _storeQtyAdded: function(elm){
                    var itemId = $(elm).data('cart-item-id');
                    this.qtyData[itemId] = parseInt(($(elm).val()));
                },
                _bind: function(){
                    var events = {},
                        self = this;
                    this._on(this.element, this.events);
                },
                _showItemUpdateButton: function(elem){
                    var itemId = elem.data('cart-item-id');
                    var itemQty = this.qtyData[itemId];
                    if (this._isValidQty(itemQty, elem.val())) {
                        var input = this._renderUpdateInput();
                        this._removeItemButton(elem);
                        $(input).insertAfter(elem);
                    } else if (elem.val() == 0) { //eslint-disable-line eqeqeq
                        this._removeItemButton(elem);
                    } else {
                        this._removeItemButton(elem);
                    }
                },
                _isValidQty: function(origin, changed){
                    return origin != changed && //eslint-disable-line eqeqeq
                        changed.length > 0 &&
                        changed - 0 == changed && //eslint-disable-line eqeqeq
                        changed - 0 > 0;
                },
                _removeItemButton: function(elem){
                    $(elem).next('button[name="update_item_qty"]').remove();
                },
                /**
                 * Input for submit form.
                 * This control shouldn't have "type=hidden", "display: none" for validation work :(
                 *
                 * @param {Object} config
                 * @private
                 */
                _renderUpdateInput: function () {
                    return '<button type="submit" name="update_item_qty" title="Update Cart" class="action update"> <span>' + $.mage.__('Update') + '</span></button>';
                },


            }
        );
        return jQuery['mage']['shoppingCart'];
    }
});