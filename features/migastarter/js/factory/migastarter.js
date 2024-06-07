angular



  .module("starter")



  .factory("Shareleads", function ($http, Url, $pwaRequest, $q, $ocLazyLoad) {

    var factory = {

      value_id: null,

      image: null,

      module: null,

    };

    factory.load = function (value_id, customer_id) {

      if (!value_id) return;

      return $http({

        method: "GET",

        url: Url.get("shareleads/mobile_view/load", {

          value_id: value_id,

          customer_id: customer_id,

        }),

        cache: false,

        responseType: "json",

      });

    };



    return factory;

  });

