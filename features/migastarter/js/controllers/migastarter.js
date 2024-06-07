angular

  .module("starter", ["ionic"])

  .controller(

    "ShareleadsViewController",

    function (

      $scope,

      $state,

      $stateParams,

      $translate,

      Loader,

      Dialog,

      Shareleads,

      Customer

    ) {

      $scope.is_loading = true;

      Loader.show();

      $scope.shareleads = {

        page_title: "Shareleads",

      };

      $scope.goToHomePage = function () {

        $state.go("home");

      };

      $scope.all_categories = {};



      $scope.loadContent = function () {

        Shareleads.load($stateParams.value_id, Customer.id)

          .success(function (data) {

            console.log("I am in mobile end.");

            console.log(data);

            $scope.is_loading = false;

            Loader.hide();

          })

          .error(function (data) {

            Dialog.alert(

              $translate.instant("Error"),

              $translate.instant(data.message),

              $translate.instant("OK")

            );

          })

          .finally(function () {

            $scope.is_loading = false;

            Loader.hide();

          });

      };

      $scope.loadContent();

    }

  );

