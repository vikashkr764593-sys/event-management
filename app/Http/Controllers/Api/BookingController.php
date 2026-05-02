<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Services\BookingService;
use App\Http\Resources\BookingResource;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;
 
class BookingController extends Controller
{
    protected $bookingService;
    protected $bookingRepo;
 
    public function __construct(BookingService $bookingService, BookingRepositoryInterface $bookingRepo)
    {
        $this->bookingService = $bookingService;
        $this->bookingRepo = $bookingRepo;
    }
    
    public function index(Request $request): JsonResponse
    { 
        try {
            return response()->json([
                'status' => true, 
                'data'   => BookingResource::collection($this->bookingRepo->all())
            ]); 
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function myBookings(Request $request): JsonResponse
    { 
        try {
            return response()->json([
                'status' => true, 
                'data'   => BookingResource::collection($this->bookingService->getUserBookings($request->user()->id))
            ]); 
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function store(StoreBookingRequest $request): JsonResponse
    {
        try {
            $booking = $this->bookingService->createBooking($request->validated(), $request->user()->id);
            return response()->json([
                'status' => true, 
                'data'   => new BookingResource($booking->load('singer'))
            ], 201);
        } catch (Exception $e) { 
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500); 
        }
    }
    
    public function show($id): JsonResponse
    { 
        try {
            $booking = $this->bookingRepo->find($id);
            return response()->json([
                'status' => true, 
                'data'   => new BookingResource($booking)
            ]);
        } catch (Exception $e) { 
            return response()->json(['status' => false, 'message' => 'Booking not found'], 404); 
        }
    }
 
    public function update(UpdateBookingRequest $request, $id): JsonResponse
    {
        try {
            $booking = $this->bookingRepo->update($id, $request->validated());
            return response()->json([
                'status' => true, 
                'data'   => new BookingResource($booking)
            ]);
        } catch (Exception $e) { 
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500); 
        }
    }
 
    public function destroy($id): JsonResponse
    {
        try {
            $this->bookingRepo->delete($id);
            return response()->json([
                'status'  => true, 
                'message' => 'Booking cancelled'
            ]);
        } catch (Exception $e) { 
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500); 
        }
    }
}
