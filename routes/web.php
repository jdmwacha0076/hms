<?php

use App\Models\Room;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\TenantsController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\ExportContractController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\SupervisorsController;
use App\Http\Middleware\SessionTimeout;

//For landing at the login page
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

//For landing at the register page
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

//For landing at the welcome page
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    SessionTimeout::class,
])->group(function () {

    ############################################################################ HOMEPAGE CONTROLLER ##############################################################################################
    //For landing at the welcome page
    Route::get('/homepage', [HomePageController::class, 'showDashboard'])->name('homepage');

    ############################################################################ HOMEPAGE CONTROLLER ##############################################################################################

    ############################################################################ EXPORT CONTROLLER ################################################################################################
    //For view contracts to export
    Route::get('/export-contracts', [ExportContractController::class, 'exportContracts'])->name('contracts.export');
    Route::get('/api/rooms/{house}', function ($houseId) {
        $rooms = Room::where('house_id', $houseId)->get();
        return response()->json(['rooms' => $rooms]);
    });

    //For exporting contracts
    Route::get('/download-contract/{id}', [ExportContractController::class, 'downloadContract'])->name('download.contract');

    //For viewing contracts
    Route::get('/export-contracts', [ExportContractController::class, 'exportContracts'])->name('contracts.export');
    Route::get('/api/rooms/{house}', function ($houseId) {
        $rooms = Room::where('house_id', $houseId)->get();
        return response()->json(['rooms' => $rooms]);
    });

    ############################################################################ EXPORT CONTROLLER #################################################################################################

    ######################################################################## CONTRACTS CONTROLLER ##################################################################################################
    //For viewing house, rooms and tenants
    Route::get('/make-contract', [ContractController::class, 'createContract'])->name('contracts.create');

    //For getting the room list from the selected house
    Route::get('/get-rooms/{house}', [ContractController::class, 'getRooms']);

    //For saving a new contract
    Route::post('/make-contact', [ContractController::class, 'saveContract'])->name('contract.save');

    //For viewing contracts
    Route::get('/view-contracts', [ContractController::class, 'viewContracts'])->name('contracts.view');
    Route::get('/api/rooms/{house}', function ($houseId) {
        $rooms = Room::where('house_id', $houseId)->get();
        return response()->json(['rooms' => $rooms]);
    });

    //For paying debt contract
    Route::put('/contract/{id}', [ContractController::class, 'updateContract'])->name('contract.update');

    //For viewing contract payment trend
    Route::get('/contracts/{id}/payments', [ContractController::class, 'viewPaymentTrend'])->name('contracts.viewPaymentTrend');


    //For updating the contract order status
    Route::patch('/contracts/{contract}/update-status', [ContractController::class, 'updateStatus'])->name('contracts.updateStatus');

    ######################################################################## CONTRACTS CONTROLLER #################################################################################################

    ######################################################################## HOUSE CONTROLLER #####################################################################################################
    //For displaying the house registration form
    Route::get('/add-house', [HouseController::class, 'createHouse'])->name('house.create');

    // For saving the house details
    Route::post('/add-house', [HouseController::class, 'saveHouse'])->name('house.save');

        //For viewing all the houses available
        Route::get('/view-house', [HouseController::class, 'viewHouses'])->name('houses.view');

    ######################################################################## HOUSE CONTROLLER ####################################################################################################

    ######################################################################## ROOM CONTROLLER #####################################################################################################

    //For viewing all houses and their rooms
    Route::get('/add-room', [RoomController::class, 'createRoom'])->name('room.create');

    //For saving a new room record
    Route::post('/save-room', [RoomController::class, 'saveRoom'])->name('room.save');

    //For viewing all the rooms available
    Route::get('/view-rooms', [RoomController::class, 'viewRooms'])->name('rooms.view');

    //For updating the room details
    Route::put('/rooms/{room}', [RoomController::class, 'updateRoomDetails'])->name('rooms.update');

    ######################################################################## ROOM CONTROLLER ###################################################################################################

    ####################################################################### TENANTS CONTROLLER #################################################################################################

    //For landing at the tenants view
    Route::get('/add-tenant', function () {
        return view('tenants.add-tenant');
    })->name('tenant.add-tenant');

    //For saving a new tenant record
    Route::post('/tenant/save', [TenantsController::class, 'saveTenant'])->name('tenant.save');

    //For viewing the list of tenants available
    Route::get('/view-tenants', [TenantsController::class, 'viewAllTenants'])->name('tenants.view-tenants');

    //For updating the tenants details
    Route::put('/tenant/{id}', [TenantsController::class, 'updateTenantDetails'])->name('tenant.update');

    //For deleting the tenant details
    Route::delete('/tenant/{id}', [TenantsController::class, 'deleteTenantDetails'])->name('tenant.delete');

    ########################################################################### TENANTS CONTROLLER ###########################################################################################

    ####################################################################### COMPLETED CONTRACTS ###############################################################################################
    //For showing view to upload signed contract
    Route::get('/upload-contracts', [ContractController::class, 'showUploadForm'])->name('contracts.upload');

    //For showing rooms after house selection
    Route::get('/get-rooms/{house}', [ContractController::class, 'getRoomsContracts']);

    //For saving an uploaded signed contract
    Route::post('/contracts/save', [ContractController::class, 'saveSignedContracts'])->name('contracts.store');

    //For displaying the signed contracts
    Route::get('/completed-contracts', [ContractController::class, 'showSignedContracts'])->name('contracts.show');

    //For exporting the signed contract into pdf format
    Route::get('/contracts/download/{id}', [ExportContractController::class, 'downloadSignedContract'])->name('contracts.download');

    ####################################################################### COMPLETED CONTRACTS ###############################################################################################

    ########################################################################## USER MANAGEMENT#################################################################################################
    // For viewing all the users
    Route::get('/user-management', [UserManagementController::class, 'viewAllUsers'])->name('user-management');

    // For updating the user details
    Route::post('/update-user', [UserManagementController::class, 'updateUser'])->name('update-user');

    //For landing at the add user page
    Route::get('/add-user', function () {
        return view('users.add-user');
    })->name('add-user');

    //For saving a new user
    Route::post('/add-user', [UserManagementController::class, 'addUser'])->name('add-user');

    ########################################################################## USER MANAGEMENT#################################################################################################

    ####################################################################### SUPERVISORS CONTROLLER ############################################################################################

    //For landing at the supervisors view
    Route::get('/add-supervisors', [SupervisorsController::class, 'addSupervisors'])->name('supervisors.add');

    //For saving supervisors details
    Route::post('/supervisor/save', [SupervisorsController::class, 'saveSupervisors'])->name('supervisors.save');

        //For viewing the list of supervisors available
        Route::get('/view-supervisors', [SupervisorsController::class, 'viewAllSuperVisors'])->name('tenants.view-supervisors');

        Route::put('/supervisors/update', [SupervisorsController::class, 'update'])->name('supervisors.update');


    ####################################################################### SUPERVISORS CONTROLLER ############################################################################################

});
