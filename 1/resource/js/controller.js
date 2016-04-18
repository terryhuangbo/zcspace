var app = angular.module('app', []);

app.controller('HeaderController', ['$scope', function ($scope) {
  $scope.Header = {
    goBack: function () {
      goBack();
    },
    favorite: function () {

    },
    share: function (title, shareData) {
      if (window.syjfree && syjfree.shareInfo) {
        syjfree.shareInfo(title, shareData);
      }
    }
  }
}]);

app.controller('ChengPinController', ['$scope', function ($scope) {
  var items = $('.items'),
    chengpins = [];

  addChengpin();

  $(document).on('click', '.quantity-decrease', function () {
    var count = parseInt($(this).next('input').val());
    console.log(count);

    if (count > 1) {
      $(this).next('input').val(count - 1);
    } else {
      $(this).next('input').val(1)
    }
    addChengpin();
  });

  $(document).on('click', '.quantity-increase', function () {
    $(this).prev('input').val(parseInt($(this).prev('input').val()) + 1);
    addChengpin();
  });

  $scope.confirmChengPin = function () {
    console.log('confirmOrder', JSON.stringify(chengpins));
    sessionStorage.setItem('chengpins', JSON.stringify(chengpins));
    goBack();
  };

  function addChengpin(){
    angular.forEach(items, function (item) {
      var chengpin = {
        extraServiceId: $(item).attr('serviceid'),
        count: $('.items').find('input').val()
      };

      chengpins.push(chengpin);
    });
  }
}]);

app.controller('ScheduleController', ['$scope', function ($scope) {
  var selectedSchedule = $scope.selectedSchedule = '2015-2-14';
  // delete after add this function
  sessionStorage.setItem('selectedSchedule', selectedSchedule);

  $scope.confirmSchedule = function () {
    sessionStorage.setItem('selectedSchedule', selectedSchedule);
    goBack();
  }
}]);

app.controller('AddressController', ['$scope', function ($scope) {
  var address = {
    name: $scope.name,
    phoneNumber: $scope.phoneNumber,
    area: $scope.area,
    detailAddress: $scope.detailAddress
  };

  var selectedAddressId = $('input[name="address"]:checked').val();

  $scope.addAddress = function () {
    console.log("new address", address);
    sessionStorage.setItem('address', address);
  };

  $scope.confirmAddress = function () {
    console.log(selectedAddressId);
    sessionStorage.setItem('selectedAddressId', selectedAddressId);
    goBack();
  }
}]);

app.controller('HomeController', ['$scope', function ($scope) {
  var comboIds = $('input[name="comboId"]').val();
  console.log(comboIds);
  $scope.order = function () {
    var order = {
      "appkey": "syq",
      "format": "json",
      "userId": $userId,
      "planId": comboIds,// 多个套餐ids,数组
      "scheduleId": sessionStorage.getItem('selectedDay') + '/' + sessionStorage.getItem('selectedTime'),
      "addressId": sessionStorage.getItem('selectedAddressId'),
      "photographerId": $photographerId,
      "extraServices": sessionStorage.getItem('chengpins'),
      "notes": "测试提交订单"
    };

    console.log('order', JSON.stringify(order));
    $('input[name="orderData"]').val(JSON.stringify(order));

    console.log($('#homeform').serialize());
    $('#homeform').submit();
  }
}]);

app.controller('ConfirmOrderController', ['$scope', function ($scope) {
  var note = $scope.note; // 备注信息

  $scope.confirmOrder = function () {
    $('input[name="appkey"]').val('syq');
    $('input[name="format"]').val('json');
    $('input[name="userId"]').val($userId);
    $('input[name="planId"]').val(sessionStorage.getItem('comboIds'));
    $('input[name="scheduleId"]').val(sessionStorage.getItem('selectedSchedule'));
    $('input[name="addressId"]').val(sessionStorage.getItem('selectedAddressId'));
    $('input[name="photographerId"]').val($photographerId);
    $('input[name="extraServices"]').val(sessionStorage.getItem('chengpins'));
    $('input[name="notes"]').val('测试提交订单');

    console.log($('#homeform').serialize());

    $('#homeform').submit();
  }
}]);

app.controller('OrderListController', ['$scope', function ($scope) {

}]);

function goBack(){
  if (window.syjfree && syjfree.backPress) {
    syjfree.backPress();
  }
}