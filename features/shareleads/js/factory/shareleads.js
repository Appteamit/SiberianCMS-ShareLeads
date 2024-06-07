angular



  .module("starter")



  .factory("Shareleads", function ($http, Url, $pwaRequest, $q, $ocLazyLoad) {

    var factory = {

      value_id: null,

      image: null,

      module: null,

    };

    factory.load = function (value_id) {
      if (!value_id) return;
      return $http({
        method: "GET",
        url: Url.get("shareleads/mobile_view/load", {
          value_id: value_id
        }),
        cache: false,
        responseType: "json",
      });
    };

    factory.contractDetail = function (value_id,contract_id) {
      if (!value_id) return;
      return $http({
        method: "GET",
        url: Url.get("shareleads/mobile_view/contract-detail", {
          value_id: value_id,
          contract_id: contract_id
        }),
        cache: false,
        responseType: "json",
      });
    };

    factory.getCode = function (value_id) {
      if (!value_id) return;
      return $http({
        method: "GET",
        url: Url.get("shareleads/mobile_view/get-code", {
          value_id: value_id
        }),
        cache: false,
        responseType: "json",
      });
    }
    factory.getSubscribers = function (value_id) {
      if (!value_id) return;
      return $http({
        method: "GET",
        url: Url.get("shareleads/mobile_view/get-subscribers", {
          value_id: value_id
        }),
        cache: false,
        responseType: "json",
      });
    }
    factory.getCustomShareText = function (value_id) {
      if (!value_id) return;
      return $http({
        method: "GET",
        url: Url.get("shareleads/mobile_view/get-custom-share-text", {
          value_id: value_id
        }),
        cache: false,
        responseType: "json",
      });
    }
    factory.getShop = function (value_id) {
      if (!value_id) return;
      return $http({
        method: "GET",
        url: Url.get("shareleads/mobile_view/get-shop", {
          value_id: value_id
        }),
        cache: false,
        responseType: "json",
      });
    }
    factory.getTransactions = function (value_id) {
      if (!value_id) return;
      return $http({
        method: "GET",
        url: Url.get("shareleads/mobile_view/get-transactions", {
          value_id: value_id
        }),
        cache: false,
        responseType: "json",
      });
    }
    factory.getMyproducts = function (value_id) {
      if (!value_id) return;
      return $http({
        method: "GET",
        url: Url.get("shareleads/mobile_view/get-my-products", {
          value_id: value_id
        }),
        cache: false,
        responseType: "json",
      });
    }
    factory.getShopDetails = function (value_id, product_id) {
      if (!value_id) return;
      return $http({
        method: "GET",
        url: Url.get("shareleads/mobile_view/get-shop-details", {
          value_id: value_id,
          product_id: product_id
        }),
        cache: false,
        responseType: "json",
      });
    }
    factory.saveCode = function (value_id,invitation_code) {
      if (!value_id) return;
      return $http({
        method: "GET",
        url: Url.get("shareleads/mobile_view/save-code", {
          value_id: value_id,
          invitation_code: invitation_code
        }),
        cache: false,
        responseType: "json",
      });
    };
    factory.buyNow = function (value_id,product_id) {
      if (!value_id) return;
      return $http({
        method: "GET",
        url: Url.get("shareleads/mobile_view/buy-now", {
          value_id: value_id,
          product_id: product_id
        }),
        cache: false,
        responseType: "json",
      });
    };



    return factory;

  });

