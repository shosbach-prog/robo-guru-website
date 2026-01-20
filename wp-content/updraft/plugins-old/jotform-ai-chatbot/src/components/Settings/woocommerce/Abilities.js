import React from 'react';
import cx from 'classnames';
import { bool } from 'prop-types';

import { ALL_TEXTS, WOO_COMMERCE_PROPERTIES } from '../../../constants';
import { useWizard } from '../../../hooks';
import { ACTION_CREATORS } from '../../../store';
import { t } from '../../../utils';
import Toggle from '../../UI/Toggle';

const Abilities = ({ isConnected }) => {
  const {
    dispatch, state
  } = useWizard();

  const { woocommerce } = state;

  const {
    [WOO_COMMERCE_PROPERTIES.PRODUCT_FILTER]: showProducts,
    [WOO_COMMERCE_PROPERTIES.PRODUCT_RECOMMENDATION]: recommendProducts,
    [WOO_COMMERCE_PROPERTIES.ADD_TO_CART]: addAndUpdateCart,
    [WOO_COMMERCE_PROPERTIES.ORDER_TRACKING]: showOrderStatus
  } = woocommerce.abilities;

  const handleToggleChange = (key, value) => {
    dispatch(ACTION_CREATORS.setWoocommerceAbility(key, value));
  };

  return (
    <div className='jfpContent-wrapper--settings-options-ability'>
      {/* chatbot abilities */}
      <h3 className='jfpContent-wrapper--settings-options-ability-title'>{t(ALL_TEXTS.CHATBOT_ABILITIES)}</h3>
      <div className={cx('jfpContent-wrapper--settings-options-ability-wrapper', { isDisabled: !isConnected })}>
        {/* show products */}
        <div className='jfpContent-wrapper--settings-options-ability-select'>
          <div>
            <h4>{t(ALL_TEXTS.FIND_PRODUCTS)}</h4>
            <p>{t(ALL_TEXTS.HELPS_CUSTOMERS_SEARCH_OR_FILTER_PRODUCTS)}</p>
          </div>
          <Toggle ariaLabel={`${t(ALL_TEXTS.FIND_PRODUCTS)} Toggle`} checked={showProducts} onChange={() => handleToggleChange(WOO_COMMERCE_PROPERTIES.PRODUCT_FILTER, !showProducts)} />
        </div>
        {/* recommend products */}
        <div className='jfpContent-wrapper--settings-options-ability-select'>
          <div>
            <h4>{t(ALL_TEXTS.RECOMMEND_PRODUCTS)}</h4>
            <p>{t(ALL_TEXTS.SUGGESTS_BEST_SELLERS)}</p>
          </div>
          <Toggle ariaLabel={`${t(ALL_TEXTS.RECOMMEND_PRODUCTS)} Toggle`} onChange={() => handleToggleChange(WOO_COMMERCE_PROPERTIES.PRODUCT_RECOMMENDATION, !recommendProducts)} />
        </div>
        {/* add & update cart */}
        <div className='jfpContent-wrapper--settings-options-ability-select'>
          <div>
            <h4>{t(ALL_TEXTS.ADD_PRODUCTS_TO_CART)}</h4>
            <p>{t(ALL_TEXTS.ADD_ITEMS_TO_THE_SHOPPING_CART)}</p>
          </div>
          <Toggle ariaLabel={`${t(ALL_TEXTS.ADD_PRODUCTS_TO_CART)} Toggle`} checked={addAndUpdateCart} onChange={() => handleToggleChange(WOO_COMMERCE_PROPERTIES.ADD_TO_CART, !addAndUpdateCart)} />
        </div>
        {/* show order status */}
        <div className='jfpContent-wrapper--settings-options-ability-select'>
          <div>
            <h4>{t(ALL_TEXTS.SHOW_ORDER_STATUS)}</h4>
            <p>{t(ALL_TEXTS.PRODVIDES_REAL_TIME_UPDATES)}</p>
          </div>
          <Toggle ariaLabel={`${t(ALL_TEXTS.SHOW_ORDER_STATUS)} Toggle`} checked={showOrderStatus} onChange={() => handleToggleChange(WOO_COMMERCE_PROPERTIES.ORDER_TRACKING, !showOrderStatus)} />
        </div>
        {/* manage refunds */}
        <div className='jfpContent-wrapper--settings-options-ability-select'>
          <div>
            <h4>{t(ALL_TEXTS.MANAGE_REFUNDS)}<span className='badge'>{t(ALL_TEXTS.COMING_SOON)}</span></h4>
            <p>{t(ALL_TEXTS.HANDLE_REFUND_REQUESTS)}</p>
          </div>
        </div>
      </div>
    </div>
  );
};

Abilities.propTypes = {
  isConnected: bool.isRequired
};

export default Abilities;
