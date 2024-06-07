angular.module("starter", ["ionic"])
  .controller('ShareleadsViewController', function ($scope, $sce, SocialSharing, $stateParams, $state, $translate, $cordovaBarcodeScanner, Dialog, Loader, Customer, Shareleads, $ionicSideMenuDelegate, $interval, GoogleMaps, LinkService, $ionicPopup, $compile, $rootScope, $window, $ionicPlatform) {
    $scope.is_loading = true;
    Loader.show();
    $scope.shareleads = {
        'page_title':  $translate.instant('Shareleads'),
    };
    $scope.filter = {
      'c_search':''
    }

    $scope.trustedHTML = function (param) { 
      return $sce.trustAsHtml(param);
    } 
    $scope.is_browser = false;
    $ionicPlatform.ready(function() {
      // Check if the platform is iOS
      if($ionicPlatform.is('ios')) {
        $scope.is_browser = false;
        console.log('Running on iOS device');
        // Your iOS-specific code here
      } 
      // Check if the platform is Android
      else if($ionicPlatform.is('android')) {
        $scope.is_browser = false;
        console.log('Running on Android device');
        // Your Android-specific code here
      } 
      // If neither iOS nor Android, it might be a browser or other platform
      else {
        $scope.is_browser = true;
        console.log('Running on other platform or browser');
        // Your code for other platforms or browsers here
      }
    });
    $scope.share = function (item) {
      console.log(item);
      $scope.is_loading = true;
      Loader.show();
      Shareleads.getShare($stateParams.value_id, item.event_id).success(function(data) {  
        console.log(data);
        
        if ($scope.is_browser) {
          console.log('Running in a browser');
          SocialSharing.share('', '', '', '', data.content + $translate.instant('APP DOWNLOAD LINK')+": "+ $scope.store_url);
          // Browser-specific logic here
        } else {
          SocialSharing.share(undefined, data.content, undefined, $translate.instant('APP DOWNLOAD LINK')+": "+ $scope.store_url, undefined);
          console.log('Not running in a browser');
          // Non-browser-specific logic here
        }
        
      }).finally(function() {
        $scope.is_loading = false;
        Loader.hide();
      });
      
    };

    $scope.home = function(){
      $state.go('home', {value_id: $stateParams.value_id});
    }
    
    $scope.is_logged_in = Customer.isLoggedIn();
    $scope.navigateTo = function(state_name) {
      $state.go(state_name, {value_id: $stateParams.value_id});
    };
    $scope.loadContent = function(is_show_preloader) {
      if (is_show_preloader) {
        $scope.is_loading = true;
          Loader.show();
      }
      Shareleads.load($stateParams.value_id).success(function(data) {
        console.log(data);
        if(data.success){
          $scope.shareleads = data;				
        }
      }).finally(function() {
        $scope.is_loading = false;
        Loader.hide();
      });
    };
		
		$scope.loginModalMP = function () {
			$scope.is_loading = false;
			Loader.hide();
			Customer.loginModal($scope, function () {
				$scope.is_logged_in = Customer.isLoggedIn();
				$scope.loadContent(true);
			});
		};
    $scope.downloadFile = function (url) {
      LinkService.openLink(url, {}, true);
    }
    $scope.openContractDetails = function (contract_id) {
      // contract_id
      $state.go('shareleads-contract-details', {value_id: $stateParams.value_id, contract_id:contract_id});
    }
		if ($scope.is_logged_in) {
			$scope.loadContent(true);
		} else {
			$scope.loginModalMP();
		}
  })
  .controller('ShareleadsContractDetailsController', function ($scope, $sce, SocialSharing, $stateParams, $state, $translate, $cordovaBarcodeScanner, Dialog, Loader, Customer, Shareleads, $ionicSideMenuDelegate, $interval, GoogleMaps, LinkService, $ionicPopup, $compile, $rootScope, $window, $ionicPlatform) {
    $scope.is_loading = true;
    Loader.show();
    $scope.shareleads = {
        'page_title':  $translate.instant('Shareleads'),
    };
    

    $scope.trustedHTML = function (param) { 
      return $sce.trustAsHtml(param);
    } 


    $scope.home = function(){
      $state.go('home', {value_id: $stateParams.value_id});
    }
    
    $scope.is_logged_in = Customer.isLoggedIn();
    $scope.navigateTo = function(state_name) {
      $state.go(state_name, {value_id: $stateParams.value_id});
    };
    $scope.loadContent = function(is_show_preloader) {
      if (is_show_preloader) {
        $scope.is_loading = true;
          Loader.show();
      }
      Shareleads.contractDetail($stateParams.value_id,$stateParams.contract_id).success(function(data) {
        console.log(data);
        if(data.success){
          $scope.shareleads = data;				
        }
      }).finally(function() {
        $scope.is_loading = false;
        Loader.hide();
      });
    };
    $scope.downloadFile = function (url) {
      LinkService.openLink(url, {}, true);
    }
		
		$scope.loginModalMP = function () {
			$scope.is_loading = false;
			Loader.hide();
			Customer.loginModal($scope, function () {
				$scope.is_logged_in = Customer.isLoggedIn();
				$scope.loadContent(true);
			});
		};
  
		if ($scope.is_logged_in) {
			$scope.loadContent(true);
		} else {
			$scope.loginModalMP();
		}
  })
  .controller('ShareleadsInvitationController', function ($scope, $sce, SocialSharing, $stateParams, $state, $translate, $cordovaBarcodeScanner, Dialog, Loader, Customer, Shareleads, $ionicSideMenuDelegate, $interval, GoogleMaps, LinkService, $ionicPopup, $compile, $rootScope, $window, $ionicPlatform) {
    $scope.is_loading = true;
    Loader.show();
    $scope.shareleads = {
        'page_title':  $translate.instant('Shareleads'),
    };
    
    $scope.loadContent = function(is_show_preloader) {
      if (is_show_preloader) {
        $scope.is_loading = true;
          Loader.show();
      }
      Shareleads.getCode($stateParams.value_id).success(function(data) {
        console.log(data);
        if(data.success){
          $scope.invitation.code = data.user.user_invitation_code;
          $scope.shareleads = data;				
        }
      }).finally(function() {
        $scope.is_loading = false;
        Loader.hide();
      });
    };
    $scope.trustedHTML = function (param) { 
      return $sce.trustAsHtml(param);
    } 


    $scope.home = function(){
      $state.go('home', {value_id: $stateParams.value_id});
    }
    
    $scope.is_logged_in = Customer.isLoggedIn();
    $scope.navigateTo = function(state_name) {
      $state.go(state_name, {value_id: $stateParams.value_id});
    };
    $scope.invitation = {'code' : ''};
    $scope.updateCode = function(code) {
      $scope.invitation.code = code;
      console.log($scope.invitation);
    };
    $scope.saveCode = function() {
      console.log($scope.invitation);
      $scope.is_loading = true;
      Loader.show();
      Shareleads.saveCode($stateParams.value_id, $scope.invitation.code).success(function(data) {
        console.log(data);
        if(data.success){
          $scope.shareleads = data;
          Dialog.alert(
              $translate.instant('Info'),
              $translate.instant(data.message),
              $translate.instant('Ok'),
            );

        }
      }).finally(function() {
        $scope.is_loading = false;
        Loader.hide();
      });
    };
    if ($scope.is_logged_in) {
			$scope.loadContent(true);
		} else {
			$scope.loginModalMP();
		}


  })
  .controller('ShareleadssubscriberController', function ($scope, $sce, SocialSharing, $stateParams, $state, $translate, $cordovaBarcodeScanner, Dialog, Loader, Customer, Shareleads, $ionicSideMenuDelegate, $interval, GoogleMaps, LinkService, $ionicPopup, $compile, $rootScope, $window, $ionicPlatform) {
    $scope.is_loading = true;
    Loader.show();
    $scope.shareleads = {
        'page_title':  $translate.instant('Shareleads'),
    };
    $scope.filter = {
      'search':''
    }
    
    $scope.loadContent = function(is_show_preloader) {
      if (is_show_preloader) {
        $scope.is_loading = true;
          Loader.show();
      }
      Shareleads.getSubscribers($stateParams.value_id).success(function(data) {
        console.log(data);
        if(data.success){
          // $scope.invitation.code = data.user.user_invitation_code;
          $scope.shareleads = data;				
        }
      }).finally(function() {
        $scope.is_loading = false;
        Loader.hide();
      });
    };
    $scope.trustedHTML = function (param) { 
      return $sce.trustAsHtml(param);
    } 
    $scope.home = function(){
      $state.go('home', {value_id: $stateParams.value_id});
    }
    
    $scope.is_logged_in = Customer.isLoggedIn();
    $scope.navigateTo = function(state_name) {
      $state.go(state_name, {value_id: $stateParams.value_id});
    };
    if ($scope.is_logged_in) {
			$scope.loadContent(true);
		} else {
			$scope.loginModalMP();
		}


  })
  .controller('ShareleadsShareController', function ($scope, $sce, SocialSharing, $stateParams, $state, $translate, $cordovaBarcodeScanner, Dialog, Loader, Customer, Shareleads, $ionicSideMenuDelegate, $interval, GoogleMaps, LinkService, $ionicPopup, $compile, $rootScope, $window, $ionicPlatform) {
    $scope.is_loading = true;
    Loader.show();
    $scope.shareleads = {
        'page_title':  $translate.instant('Shareleads'),
    };
    
    $scope.is_browser = false;
		$ionicPlatform.ready(function() {
			// Check if the platform is iOS
			if($ionicPlatform.is('ios')) {
				$scope.is_browser = false;
				console.log('Running on iOS device');
				// Your iOS-specific code here
			} 
			// Check if the platform is Android
			else if($ionicPlatform.is('android')) {
				$scope.is_browser = false;
				console.log('Running on Android device');
				// Your Android-specific code here
			} 
			// If neither iOS nor Android, it might be a browser or other platform
			else {
				$scope.is_browser = true;
				console.log('Running on other platform or browser');
				// Your code for other platforms or browsers here
			}
		});
    $scope.shareText = function() {
      if ($scope.is_browser) {
        console.log('Running in a browser');
        SocialSharing.share('', '', '', '', $scope.shareleads.text.share_text);
        // Browser-specific logic here
      } else {
        SocialSharing.share(undefined, $scope.shareleads.text.share_text, undefined, undefined, undefined);
        console.log('Not running in a browser');
        // Non-browser-specific logic here
      }
    }
    $scope.loadContent = function(is_show_preloader) {
      if (is_show_preloader) {
        $scope.is_loading = true;
          Loader.show();
      }
      Shareleads.getCustomShareText($stateParams.value_id).success(function(data) {
        console.log(data);
        if(data.success){
          // $scope.invitation.code = data.user.user_invitation_code;
          $scope.shareleads = data;				
        }
      }).finally(function() {
        $scope.is_loading = false;
        Loader.hide();
      });
    };
    $scope.trustedHTML = function (param) { 
      return $sce.trustAsHtml(param);
    } 
    $scope.home = function(){
      $state.go('home', {value_id: $stateParams.value_id});
    }
    
    $scope.is_logged_in = Customer.isLoggedIn();
    $scope.navigateTo = function(state_name) {
      $state.go(state_name, {value_id: $stateParams.value_id});
    };
    if ($scope.is_logged_in) {
			$scope.loadContent(true);
		} else {
			$scope.loginModalMP();
		}


  })
  .controller('ShareleadsShopController', function ($scope, $sce, SocialSharing, $stateParams, $state, $translate, $cordovaBarcodeScanner, Dialog, Loader, Customer, Shareleads, $ionicSideMenuDelegate, $interval, GoogleMaps, LinkService, $ionicPopup, $compile, $rootScope, $window, $ionicPlatform) {
    $scope.is_loading = true;
    Loader.show();
    $scope.shareleads = {
        'page_title':  $translate.instant('Shareleads'),
    };
    $scope.filter = {
      'search':''
    }
    // Function to chunk array into shops
    $scope.chunkArray = function(array, chunkSize) {
      var chunks = [];
      for (var i = 0; i < array.length; i += chunkSize) {
        chunks.push(array.slice(i, i + chunkSize));
      }
      return chunks;
    };
    $scope.loadContent = function(is_show_preloader) {
      if (is_show_preloader) {
        $scope.is_loading = true;
          Loader.show();
      }
      Shareleads.getShop($stateParams.value_id).success(function(data) {
        console.log(data);
        if(data.success){
          // $scope.invitation.code = data.user.user_invitation_code;
          $scope.shops = $scope.chunkArray(data.shops, 2);
          $scope.shareleads = data;				
        }
      }).finally(function() {
        $scope.is_loading = false;
        Loader.hide();
      });
    };
    $scope.trustedHTML = function (param) { 
      return $sce.trustAsHtml(param);
    } 
    $scope.home = function(){
      $state.go('home', {value_id: $stateParams.value_id});
    }
    $scope.shopDetails = function(product_id){
      $state.go('shareleads-shop_details', {value_id: $stateParams.value_id, product_id:product_id});
    }
    
    $scope.is_logged_in = Customer.isLoggedIn();
    $scope.navigateTo = function(state_name) {
      $state.go(state_name, {value_id: $stateParams.value_id});
    };
    if ($scope.is_logged_in) {
			$scope.loadContent(true);
		} else {
			$scope.loginModalMP();
		}


  })
  .controller('ShareleadsShopDetailsController', function ($scope, $sce, SocialSharing, $stateParams, $state, $translate, $cordovaBarcodeScanner, Dialog, Loader, Customer, Shareleads, $ionicSideMenuDelegate, $interval, GoogleMaps, LinkService, $ionicPopup, $compile, $rootScope, $window, $ionicPlatform) {
    $scope.is_loading = true;
    Loader.show();
    $scope.shareleads = {
        'page_title':  $translate.instant('Shareleads'),
    };
    $scope.loadContent = function(is_show_preloader) {
      if (is_show_preloader) {
        $scope.is_loading = true;
          Loader.show();
      }
      Shareleads.getShopDetails($stateParams.value_id, $stateParams.product_id).success(function(data) {
        console.log(data);
        if(data.success){
          $scope.shareleads = data;			
          $scope.product = data.product;				
        }
      }).finally(function() {
        $scope.is_loading = false;
        Loader.hide();
      });
    };
    $scope.trustedHTML = function (param) { 
      return $sce.trustAsHtml(param);
    } 
    $scope.buyNow = function(product_id){
      Shareleads.buyNow($stateParams.value_id, product_id).success(function(data) {
        if(data.success){
          $scope.product.redeem = 1;
          Dialog.alert(
						'Success',
						data.message,
						'Ok'
          );
          console.log(product_id, data);
        } else {
          Dialog.alert(
						'Error',
					  data.message,
						'Ok'
          );
        }
      }).error(function(data) {
        console.log(data);
        Dialog.alert(
          'Error',
          data.message,
          'Ok'
        );
      }).finally(function() {
        $scope.is_loading = false;
        Loader.hide();
      });
    }
    $scope.home = function(){
      $state.go('home', {value_id: $stateParams.value_id});
    }
    
    $scope.is_logged_in = Customer.isLoggedIn();
    $scope.navigateTo = function(state_name) {
      $state.go(state_name, {value_id: $stateParams.value_id});
    };
    if ($scope.is_logged_in) {
			$scope.loadContent(true);
		} else {
			$scope.loginModalMP();
		}


  })
  .controller('ShareleadsTransactionsController', function ($scope, $sce, SocialSharing, $stateParams, $state, $translate, $cordovaBarcodeScanner, Dialog, Loader, Customer, Shareleads, $ionicSideMenuDelegate, $interval, GoogleMaps, LinkService, $ionicPopup, $compile, $rootScope, $window, $ionicPlatform) {
    $scope.is_loading = true;
    Loader.show();
    $scope.shareleads = {
        'page_title':  $translate.instant('Shareleads'),
    };
    $scope.filter = {
      'search':''
    }
    $scope.loadContent = function(is_show_preloader) {
      if (is_show_preloader) {
        $scope.is_loading = true;
          Loader.show();
      }
      Shareleads.getTransactions($stateParams.value_id).success(function(data) {
        console.log(data);
        if(data.success){
          // $scope.invitation.code = data.user.user_invitation_code;
          $scope.shareleads = data;				
        }
      }).finally(function() {
        $scope.is_loading = false;
        Loader.hide();
      });
    };
    $scope.trustedHTML = function (param) { 
      return $sce.trustAsHtml(param);
    } 
    $scope.home = function(){
      $state.go('home', {value_id: $stateParams.value_id});
    }
    
    $scope.is_logged_in = Customer.isLoggedIn();
    $scope.navigateTo = function(state_name) {
      $state.go(state_name, {value_id: $stateParams.value_id});
    };
    if ($scope.is_logged_in) {
			$scope.loadContent(true);
		} else {
			$scope.loginModalMP();
		}


  })
  .controller('ShareleadsMyproductsController', function ($scope, $sce, SocialSharing, $stateParams, $state, $translate, $cordovaBarcodeScanner, Dialog, Loader, Customer, Shareleads, $ionicSideMenuDelegate, $interval, GoogleMaps, LinkService, $ionicPopup, $compile, $rootScope, $window, $ionicPlatform) {
    $scope.is_loading = true;
    Loader.show();
    $scope.shareleads = {
        'page_title':  $translate.instant('Shareleads'),
    };
    $scope.filter = {
      'search':''
    }
    $scope.chunkArray = function(array, chunkSize) {
      var chunks = [];
      for (var i = 0; i < array.length; i += chunkSize) {
        chunks.push(array.slice(i, i + chunkSize));
      }
      return chunks;
    };
    $scope.loadContent = function(is_show_preloader) {
      if (is_show_preloader) {
        $scope.is_loading = true;
          Loader.show();
      }
      Shareleads.getMyproducts($stateParams.value_id).success(function(data) {
        console.log(data);
        if(data.success){
          $scope.shops = $scope.chunkArray(data.shops, 2);
          $scope.shareleads = data;		
        }
      }).finally(function() {
        $scope.is_loading = false;
        Loader.hide();
      });
    };
    $scope.shopDetails = function(product_id){
      $state.go('shareleads-shop_details', {value_id: $stateParams.value_id, product_id:product_id});
    }
    $scope.trustedHTML = function (param) { 
      return $sce.trustAsHtml(param);
    } 
    $scope.home = function(){
      $state.go('home', {value_id: $stateParams.value_id});
    }
    
    $scope.is_logged_in = Customer.isLoggedIn();
    $scope.navigateTo = function(state_name) {
      $state.go(state_name, {value_id: $stateParams.value_id});
    };
    if ($scope.is_logged_in) {
			$scope.loadContent(true);
		} else {
			$scope.loginModalMP();
		}


  });

