@extends('layouts.admin')
@push('script-page')
@endpush
@section('page-title')
    {{__('Support')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 ">{{__('Support')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Support')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        <a href="{{ route('support.grid') }}" class="btn btn-sm btn-primary-subtle me-1" data-bs-toggle="tooltip" title="{{__('Grid View')}}">
            <i class="ti ti-layout-grid text-white"></i>
        </a>
        <a href="#" data-size="lg" data-url="{{ route('support.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create')}}" data-title="{{__('Create Support')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>
    </div>
@endsection

@section('content')
<div class="row mb-4 gy-4">
    <div class="col-xxl-3 col-xl-4 col-sm-6 col-12 support-ticket-card">
        <div class="support-card-inner d-flex align-items-start gap-3">
          
            <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                    fill="#FF3A6E"></path>
            </svg>
            <div class="support-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M23.0002 12.9999C23.2502 12.9999 23.5003 12.7498 23.5003 12.4998V9.49976C23.5003 8.64968 22.8504 7.99976 22.0003 7.99976H18V21.9999H21.9998C22.8499 21.9999 23.4998 21.35 23.4998 20.4999V17.4999C23.4996 17.3673 23.4468 17.2403 23.3531 17.1465C23.2593 17.0528 23.1323 17 22.9997 16.9998C22.4746 16.9916 21.9738 16.7773 21.6054 16.4032C21.237 16.029 21.0305 15.5249 21.0305 14.9998C21.0305 14.4747 21.237 13.9707 21.6054 13.5965C21.9738 13.2223 22.4751 13.008 23.0002 12.9999Z" fill="#FF3A6E"/>
                    <path d="M22.6496 6.50021C22.8997 6.40037 23.0494 6.10037 22.9496 5.85029L21.9493 3.00005C21.6992 2.19989 20.7973 1.80005 20.0494 2.05013L5.84961 6.94997H21.8495C22.0866 6.75262 22.3578 6.60021 22.6496 6.50021Z" fill="#FF3A6E"/>
                    <path d="M0.5 9.50049V12.5004C0.5 12.8004 0.70016 13.0006 1.00016 13.0006C1.2654 12.9965 1.52881 13.0452 1.77505 13.1439C2.02129 13.2425 2.24545 13.3892 2.43447 13.5753C2.6235 13.7614 2.77361 13.9833 2.87607 14.2279C2.97854 14.4726 3.0313 14.7353 3.0313 15.0005C3.0313 15.2658 2.97854 15.5284 2.87607 15.7731C2.77361 16.0178 2.6235 16.2396 2.43447 16.4258C2.24545 16.6119 2.02129 16.7585 1.77505 16.8572C1.52881 16.9559 1.2654 17.0046 1.00016 17.0004C0.70016 17.0004 0.5 17.2006 0.5 17.5006V20.5006C0.5 21.3507 1.14992 22.0006 2 22.0006H17V8.00049H2C1.15184 8.00049 0.5 8.65041 0.5 9.50049ZM6.5 12.0003H9.5C9.8 12.0003 10.0002 12.2004 10.0002 12.5004C10.0002 12.8004 9.8 13.0006 9.5 13.0006H6.5C6.2 13.0006 5.99984 12.8004 5.99984 12.5004C5.99984 12.2004 6.2 12.0003 6.5 12.0003ZM6.5 14.5001H12.9997C13.2997 14.5001 13.4998 14.7003 13.4998 15.0003C13.4998 15.3003 13.2997 15.5004 12.9997 15.5004H6.5C6.2 15.5004 5.99984 15.3003 5.99984 15.0003C5.99984 14.7003 6.2 14.5001 6.5 14.5001ZM6.5 17H12.9997C13.2997 17 13.4998 17.2001 13.4998 17.5001C13.4998 17.8001 13.2997 18.0003 12.9997 18.0003H6.5C6.2 18.0003 5.99984 17.8001 5.99984 17.5001C5.99984 17.2001 6.2 17 6.5 17Z" fill="#FF3A6E"/>
                    <rect x="5" y="11" width="9" height="8" fill="#FF3A6E"/>
                    <g clip-path="url(#clip0_1935_37)">
                    <path class="white-icon" d="M13.162 12.8365C13.4373 12.5612 13.4373 12.1133 13.162 11.8381C12.8867 11.5628 12.4388 11.5628 12.1635 11.8381C11.8876 12.114 11.8875 12.5606 12.1635 12.8365C12.4388 13.1118 12.8867 13.1118 13.162 12.8365Z" fill="white"/>
                    <path class="white-icon" d="M7.50372 12.5037C8.59754 11.4099 10.2568 11.1729 11.5919 11.8481C11.6584 11.7029 11.7537 11.5728 11.8737 11.4641C10.3489 10.6548 8.42949 10.9123 7.17089 12.1709C5.91229 13.4295 5.65483 15.3489 6.46412 16.8738C6.56949 16.7572 6.69889 16.6602 6.84814 16.592C6.17291 15.2568 6.40991 13.5975 7.50372 12.5037Z" fill="white"/>
                    <path class="white-icon" d="M13.5357 13.1262C13.4296 13.2435 13.3001 13.3401 13.1516 13.4079C13.8269 14.7431 13.5899 16.4024 12.4961 17.4962C11.4022 18.5901 9.74292 18.8271 8.40776 18.1518C8.34144 18.2969 8.24627 18.4271 8.12598 18.536C9.65035 19.345 11.5697 19.0883 12.8289 17.8291C14.0875 16.5705 14.345 14.6511 13.5357 13.1262Z" fill="white"/>
                    <path class="white-icon" d="M6.8383 17.1633C6.56301 17.4385 6.56301 17.8865 6.8383 18.1617C7.11352 18.437 7.56144 18.4371 7.83678 18.1618L7.83679 18.1617C8.11208 17.8865 8.11208 17.4385 7.83679 17.1633C7.56088 16.8873 7.11429 16.8873 6.8383 17.1633Z" fill="white"/>
                    <path class="white-icon" d="M12.5887 15.5367V14.4632L12.0054 14.3173C11.976 14.2309 11.941 14.1464 11.9007 14.0645L12.21 13.5489L11.4509 12.7898L10.9353 13.0992C10.8534 13.0588 10.769 13.0238 10.6825 12.9944L10.5367 12.4111H9.46315L9.31733 12.9944C9.2309 13.0238 9.14642 13.0588 9.06452 13.0992L8.54893 12.7898L7.78982 13.5489L8.09919 14.0645C8.05882 14.1464 8.02383 14.2309 7.99443 14.3173L7.41113 14.4632V15.5367L7.99443 15.6825C8.02383 15.769 8.05883 15.8534 8.09919 15.9353L7.78983 16.4509L8.54893 17.21L9.06452 16.9007C9.14642 16.941 9.2309 16.976 9.31733 17.0054L9.46315 17.5887H10.5367L10.6825 17.0054C10.769 16.976 10.8534 16.941 10.9353 16.9007L11.4509 17.21L12.21 16.4509L11.9007 15.9353C11.941 15.8534 11.976 15.769 12.0054 15.6825L12.5887 15.5367ZM9.99991 15.9421C9.48043 15.9421 9.05778 15.5194 9.05778 14.9999C9.05778 14.4804 9.48043 14.0578 9.99991 14.0578C10.5194 14.0578 10.9421 14.4804 10.9421 14.9999C10.9421 15.5194 10.5194 15.9421 9.99991 15.9421Z" fill="white"/>
                    </g>
                    <defs>
                    <clipPath id="clip0_1935_37">
                    <rect width="8" height="8" fill="white" transform="translate(6 11)"/>
                    </clipPath>
                    </defs>
                </svg>
            </div>
            <div class="support-content flex-1">
                <span class="text-sm d-block mb-1">{{ __('Total') }}</span>
                <h2 class="h5 mb-0">{{ __('Ticket') }}</h2>
            </div>
            <h3 class="h4 mb-0">{{ $countTicket }}</h3>
        </div>
    </div>
    <div class="col-xxl-3 col-xl-4 col-sm-6 col-12 support-ticket-card">
        <div class="support-card-inner d-flex align-items-start gap-3">
            
            <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                    fill="#FF3A6E"></path>
            </svg>
            <div class="support-icon">
                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_1932_682)">
                    <path d="M23.6003 12.9999C23.8503 12.9999 24.1004 12.7498 24.1004 12.4998V9.49976C24.1004 8.64968 23.4505 7.99976 22.6004 7.99976H18.6001V21.9999H22.5999C23.45 21.9999 24.0999 21.35 24.0999 20.4999V17.4999C24.0997 17.3673 24.0469 17.2403 23.9532 17.1465C23.8594 17.0528 23.7324 17 23.5998 16.9998C23.0747 16.9916 22.5739 16.7773 22.2055 16.4032C21.8371 16.029 21.6306 15.5249 21.6306 14.9998C21.6306 14.4747 21.8371 13.9707 22.2055 13.5965C22.5739 13.2223 23.0752 13.008 23.6003 12.9999Z" fill="#0CAF60"/>
                    <path d="M23.2492 6.50021C23.4993 6.40037 23.649 6.10037 23.5492 5.85029L22.5489 3.00005C22.2988 2.19989 21.3969 1.80005 20.649 2.05013L6.44922 6.94997H22.4491C22.6862 6.75262 22.9574 6.60021 23.2492 6.50021Z" fill="#0CAF60"/>
                    <path d="M1.09961 9.50049V12.5004C1.09961 12.8004 1.29977 13.0006 1.59977 13.0006C1.86501 12.9965 2.12842 13.0452 2.37466 13.1439C2.6209 13.2425 2.84506 13.3892 3.03408 13.5753C3.22311 13.7614 3.37322 13.9833 3.47568 14.2279C3.57815 14.4726 3.63091 14.7353 3.63091 15.0005C3.63091 15.2658 3.57815 15.5284 3.47568 15.7731C3.37322 16.0178 3.22311 16.2396 3.03408 16.4258C2.84506 16.6119 2.6209 16.7585 2.37466 16.8572C2.12842 16.9559 1.86501 17.0046 1.59977 17.0004C1.29977 17.0004 1.09961 17.2006 1.09961 17.5006V20.5006C1.09961 21.3507 1.74953 22.0006 2.59961 22.0006H17.5996V8.00049H2.59961C1.75145 8.00049 1.09961 8.65041 1.09961 9.50049ZM7.09961 12.0003H10.0996C10.3996 12.0003 10.5998 12.2004 10.5998 12.5004C10.5998 12.8004 10.3996 13.0006 10.0996 13.0006H7.09961C6.79961 13.0006 6.59945 12.8004 6.59945 12.5004C6.59945 12.2004 6.79961 12.0003 7.09961 12.0003ZM7.09961 14.5001H13.5993C13.8993 14.5001 14.0994 14.7003 14.0994 15.0003C14.0994 15.3003 13.8993 15.5004 13.5993 15.5004H7.09961C6.79961 15.5004 6.59945 15.3003 6.59945 15.0003C6.59945 14.7003 6.79961 14.5001 7.09961 14.5001ZM7.09961 17H13.5993C13.8993 17 14.0994 17.2001 14.0994 17.5001C14.0994 17.8001 13.8993 18.0003 13.5993 18.0003H7.09961C6.79961 18.0003 6.59945 17.8001 6.59945 17.5001C6.59945 17.2001 6.79961 17 7.09961 17Z" fill="#0CAF60"/>
                    <rect x="5.59961" y="11" width="9" height="8" fill="#0CAF60"/>
                    <path class="white-icon" d="M14 13.5915C14.0003 13.6691 13.983 13.7461 13.9492 13.8179C13.9154 13.8897 13.8658 13.9548 13.8031 14.0096L9.56344 17.7388C9.37273 17.906 9.11441 18 8.84509 18C8.57578 18 8.31745 17.906 8.12674 17.7388L6.19861 16.0428C6.13582 15.9879 6.08594 15.9227 6.05187 15.851C6.01778 15.7791 6.00015 15.7021 6 15.6242C5.99984 15.5464 6.01716 15.4693 6.05095 15.3973C6.08474 15.3254 6.13436 15.26 6.19693 15.205C6.25951 15.1499 6.33383 15.1063 6.41562 15.0766C6.49741 15.0468 6.58508 15.0316 6.67357 15.0317C6.76208 15.0319 6.84967 15.0474 6.93134 15.0774C7.01301 15.1073 7.08715 15.1512 7.14951 15.2065L8.84509 16.6979L12.8523 13.1732C12.9463 13.0905 13.0661 13.0342 13.1966 13.0114C13.327 12.9885 13.4622 13.0003 13.585 13.045C13.7079 13.0898 13.8128 13.1656 13.8868 13.2629C13.9607 13.3601 14.0001 13.4745 14 13.5915Z" fill="white"/>
                    </g>
                    <defs>
                    <clipPath id="clip0_1932_682">
                    <rect width="24" height="24" fill="white" transform="translate(0.600098)"/>
                    </clipPath>
                    </defs>
                </svg>
            </div>
            <div class="support-content flex-1">
                <span class="text-sm d-block mb-1">{{ __('Open') }}</span>
                <h2 class="h5 mb-0">{{ __('Ticket') }}</h2>
            </div>
            <h3 class="h4 mb-0">{{ $countOpenTicket }}</h3>
        </div>
    </div>
    <div class="col-xxl-3 col-xl-4 col-sm-6 col-12 support-ticket-card">
        <div class="support-card-inner d-flex align-items-start gap-3">
             
            <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                    fill="#FF3A6E"></path>
            </svg>
            <div class="support-icon">
                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_1932_794)">
                    <path d="M23.4001 12.9999C23.6501 12.9999 23.9002 12.7498 23.9002 12.4998V9.49976C23.9002 8.64968 23.2503 7.99976 22.4002 7.99976H18.3999V21.9999H22.3997C23.2498 21.9999 23.8997 21.35 23.8997 20.4999V17.4999C23.8995 17.3673 23.8467 17.2403 23.753 17.1465C23.6592 17.0528 23.5322 17 23.3996 16.9998C22.8745 16.9916 22.3737 16.7773 22.0053 16.4032C21.6369 16.029 21.4304 15.5249 21.4304 14.9998C21.4304 14.4747 21.6369 13.9707 22.0053 13.5965C22.3737 13.2223 22.875 13.008 23.4001 12.9999Z" fill="#FFA21D"/>
                    <path d="M23.0495 6.50021C23.2996 6.40037 23.4493 6.10037 23.3495 5.85029L22.3492 3.00005C22.0991 2.19989 21.1972 1.80005 20.4493 2.05013L6.24951 6.94997H22.2494C22.4865 6.75262 22.7577 6.60021 23.0495 6.50021Z" fill="#FFA21D"/>
                    <path d="M0.899902 9.50049V12.5004C0.899902 12.8004 1.10006 13.0006 1.40006 13.0006C1.6653 12.9965 1.92871 13.0452 2.17495 13.1439C2.42119 13.2425 2.64535 13.3892 2.83437 13.5753C3.0234 13.7614 3.17351 13.9833 3.27597 14.2279C3.37844 14.4726 3.4312 14.7353 3.4312 15.0005C3.4312 15.2658 3.37844 15.5284 3.27597 15.7731C3.17351 16.0178 3.0234 16.2396 2.83437 16.4258C2.64535 16.6119 2.42119 16.7585 2.17495 16.8572C1.92871 16.9559 1.6653 17.0046 1.40006 17.0004C1.10006 17.0004 0.899902 17.2006 0.899902 17.5006V20.5006C0.899902 21.3507 1.54982 22.0006 2.3999 22.0006H17.3999V8.00049H2.3999C1.55174 8.00049 0.899902 8.65041 0.899902 9.50049ZM6.8999 12.0003H9.8999C10.1999 12.0003 10.4001 12.2004 10.4001 12.5004C10.4001 12.8004 10.1999 13.0006 9.8999 13.0006H6.8999C6.5999 13.0006 6.39974 12.8004 6.39974 12.5004C6.39974 12.2004 6.5999 12.0003 6.8999 12.0003ZM6.8999 14.5001H13.3996C13.6996 14.5001 13.8997 14.7003 13.8997 15.0003C13.8997 15.3003 13.6996 15.5004 13.3996 15.5004H6.8999C6.5999 15.5004 6.39974 15.3003 6.39974 15.0003C6.39974 14.7003 6.5999 14.5001 6.8999 14.5001ZM6.8999 17H13.3996C13.6996 17 13.8997 17.2001 13.8997 17.5001C13.8997 17.8001 13.6996 18.0003 13.3996 18.0003H6.8999C6.5999 18.0003 6.39974 17.8001 6.39974 17.5001C6.39974 17.2001 6.5999 17 6.8999 17Z" fill="#FFA21D"/>
                    <rect x="5.3999" y="11" width="9" height="8" fill="#FFA21D"/>
                    <g clip-path="url(#clip1_1932_794)">
                    <path class="white-icon" fill-rule="evenodd" clip-rule="evenodd" d="M9.47924 14.8253C9.47788 14.8229 9.00743 13.9791 8.52712 13.9178C8.04256 13.856 7.78319 14.2282 8.064 14.6268C8.33785 15.0654 8.57725 15.9363 8.63276 16.4369C8.81148 18.0495 9.60437 19 11.2845 19C12.0945 19 13.1264 18.7526 13.6833 18.1252C13.9128 17.8667 14.0517 17.5491 14.0517 17.1719V13.138C14.0517 12.9405 13.8897 12.7785 13.6922 12.7785H13.5183C13.3208 12.7785 13.1588 12.9405 13.1588 13.138V15.1318H12.977V11.9035C12.977 11.684 12.797 11.5041 12.5776 11.5041H12.3883C12.1689 11.5041 11.989 11.684 11.989 11.9035V14.7514H11.8071V11.3994C11.8071 11.1799 11.6272 11 11.4078 11H11.2185C10.9991 11 10.8192 11.18 10.8192 11.3994V14.6583H10.6373V11.9795C10.6373 11.7601 10.4574 11.5802 10.238 11.5802H10.0487C9.82924 11.5802 9.64934 11.7601 9.64934 11.9795V14.781L9.47924 14.8253Z" fill="white"/>
                    </g>
                    </g>
                    <defs>
                    <clipPath id="clip0_1932_794">
                    <rect width="24" height="24" fill="white" transform="translate(0.399902)"/>
                    </clipPath>
                    <clipPath id="clip1_1932_794">
                    <rect width="8" height="8" fill="white" transform="translate(7 11)"/>
                    </clipPath>
                    </defs>
                </svg>
            </div>
            <div class="support-content flex-1">
                <span class="text-sm d-block mb-1">{{ __('On Hold') }}</span>
                <h2 class="h5 mb-0">{{ __('Ticket') }}</h2>
            </div>
            <h3 class="h4 mb-0">{{ $countonholdTicket }}</h3>
        </div>
    </div>
    <div class="col-xxl-3 col-xl-4 col-sm-6 col-12 support-ticket-card">
        <div class="support-card-inner d-flex align-items-start gap-3">
            
            <svg class="bottom-svg" width="135" height="80" viewBox="0 0 135 80" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M74.7692 35C27.8769 35 5.38462 65 0 80H135.692V0C134.923 11.6667 121.662 35 74.7692 35Z"
                    fill="#FF3A6E"></path>
            </svg>
            <div class="support-icon">
                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_1932_785)">
                    <path d="M23.2004 12.9999C23.4504 12.9999 23.7005 12.7498 23.7005 12.4998V9.49976C23.7005 8.64968 23.0506 7.99976 22.2005 7.99976H18.2002V21.9999H22.2C23.0501 21.9999 23.7 21.35 23.7 20.4999V17.4999C23.6998 17.3673 23.647 17.2403 23.5533 17.1465C23.4595 17.0528 23.3325 17 23.1999 16.9998C22.6748 16.9916 22.174 16.7773 21.8056 16.4032C21.4372 16.029 21.2307 15.5249 21.2307 14.9998C21.2307 14.4747 21.4372 13.9707 21.8056 13.5965C22.174 13.2223 22.6753 13.008 23.2004 12.9999Z" fill="#3EC9D6"/>
                    <path d="M22.8493 6.50021C23.0994 6.40037 23.2491 6.10037 23.1493 5.85029L22.149 3.00005C21.8989 2.19989 20.997 1.80005 20.2491 2.05013L6.04932 6.94997H22.0492C22.2863 6.75262 22.5575 6.60021 22.8493 6.50021Z" fill="#3EC9D6"/>
                    <path d="M0.699707 9.50049V12.5004C0.699707 12.8004 0.899867 13.0006 1.19987 13.0006C1.46511 12.9965 1.72852 13.0452 1.97476 13.1439C2.221 13.2425 2.44516 13.3892 2.63418 13.5753C2.82321 13.7614 2.97332 13.9833 3.07578 14.2279C3.17825 14.4726 3.23101 14.7353 3.23101 15.0005C3.23101 15.2658 3.17825 15.5284 3.07578 15.7731C2.97332 16.0178 2.82321 16.2396 2.63418 16.4258C2.44516 16.6119 2.221 16.7585 1.97476 16.8572C1.72852 16.9559 1.46511 17.0046 1.19987 17.0004C0.899867 17.0004 0.699707 17.2006 0.699707 17.5006V20.5006C0.699707 21.3507 1.34963 22.0006 2.19971 22.0006H17.1997V8.00049H2.19971C1.35155 8.00049 0.699707 8.65041 0.699707 9.50049ZM6.69971 12.0003H9.69971C9.99971 12.0003 10.1999 12.2004 10.1999 12.5004C10.1999 12.8004 9.99971 13.0006 9.69971 13.0006H6.69971C6.39971 13.0006 6.19955 12.8004 6.19955 12.5004C6.19955 12.2004 6.39971 12.0003 6.69971 12.0003ZM6.69971 14.5001H13.1994C13.4994 14.5001 13.6995 14.7003 13.6995 15.0003C13.6995 15.3003 13.4994 15.5004 13.1994 15.5004H6.69971C6.39971 15.5004 6.19955 15.3003 6.19955 15.0003C6.19955 14.7003 6.39971 14.5001 6.69971 14.5001ZM6.69971 17H13.1994C13.4994 17 13.6995 17.2001 13.6995 17.5001C13.6995 17.8001 13.4994 18.0003 13.1994 18.0003H6.69971C6.39971 18.0003 6.19955 17.8001 6.19955 17.5001C6.19955 17.2001 6.39971 17 6.69971 17Z" fill="#3EC9D6"/>
                    <rect x="5.19971" y="11" width="9" height="8" fill="#3EC9D6"/>
                    <g clip-path="url(#clip1_1932_785)">
                    <path class="white-icon" d="M10.861 15.0003L13.1043 12.7569C13.166 12.6951 13.2001 12.6127 13.2002 12.5249C13.2002 12.437 13.1661 12.3545 13.1043 12.2928L12.9078 12.0963C12.846 12.0344 12.7636 12.0005 12.6756 12.0005C12.5878 12.0005 12.5054 12.0344 12.4436 12.0963L10.2003 14.3396L7.95688 12.0963C7.89517 12.0344 7.81273 12.0005 7.72483 12.0005C7.63702 12.0005 7.55459 12.0344 7.49288 12.0963L7.2962 12.2928C7.1682 12.4208 7.1682 12.629 7.2962 12.7569L9.53956 15.0003L7.2962 17.2436C7.23444 17.3055 7.20044 17.3879 7.20044 17.4757C7.20044 17.5636 7.23444 17.646 7.2962 17.7078L7.49283 17.9043C7.55454 17.9661 7.63702 18.0001 7.72478 18.0001C7.81268 18.0001 7.89512 17.9661 7.95683 17.9043L10.2002 15.661L12.4436 17.9043C12.5054 17.9661 12.5878 18.0001 12.6756 18.0001H12.6757C12.7635 18.0001 12.8459 17.9661 12.9077 17.9043L13.1043 17.7078C13.166 17.646 13.2 17.5636 13.2 17.4757C13.2 17.3879 13.166 17.3055 13.1043 17.2437L10.861 15.0003Z" fill="white"/>
                    </g>
                    </g>
                    <defs>
                    <clipPath id="clip0_1932_785">
                    <rect width="24" height="24" fill="white" transform="translate(0.200195)"/>
                    </clipPath>
                    <clipPath id="clip1_1932_785">
                    <rect width="6" height="6" fill="white" transform="translate(7.2002 12)"/>
                    </clipPath>
                    </defs>
                </svg>
            </div>
            <div class="support-content flex-1">
                <span class="text-sm d-block mb-1">{{ __('Close') }}</span>
                <h2 class="h5 mb-0">{{ __('Ticket') }}</h2>
            </div>
            <h3 class="h4 mb-0">{{ $countCloseTicket }}</h3>
        </div>
    </div>
</div>


    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th scope="col">{{__('Created By')}}</th>
                                <th scope="col">{{__('Ticket')}}</th>
                                <th scope="col">{{__('Code')}}</th>
                                <th scope="col">{{__('Attachment')}}</th>
                                <th scope="col">{{__('Assign User')}}</th>
                                <th scope="col">{{__('Status')}}</th>
                                <th scope="col">{{__('Created At')}}</th>
                                <th scope="col" >{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody class="list">
                            @php
                                $supportpath=\App\Models\Utility::get_file('uploads/supports');
                            @endphp
                            @foreach($supports as $support)
                                <tr>
                                    <td scope="row">
                                        <div class="media align-items-center">
                                            <div>
                                                <div class="avatar-parent-child">
                                                    <img alt="" class="avatar rounded border-2 border border-primary avatar-sm me-1" @if(!empty($support->createdBy) && !empty($support->createdBy->avatar)) src="{{asset(Storage::url('uploads/avatar')).'/'.$support->createdBy->avatar}}" @else  src="{{asset(Storage::url('uploads/avatar')).'/avatar.png'}}" @endif>
                                                    @if($support->replyUnread()>0)
                                                        <span class="avatar-child avatar-badge bg-success"></span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="media-body">
                                                {{!empty($support->createdBy)?$support->createdBy->name:''}}
                                            </div>
                                        </div>
                                    </td>
                                    <td scope="row">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <a href="{{ route('support.reply',\Crypt::encrypt($support->id)) }}" class="name h6 mb-2 d-block text-sm">{{$support->subject}}</a>
                                                @if($support->priority == 0)
                                                    <span data-toggle="tooltip" data-title="{{__('Priority')}}" class="text-capitalize status_badge badge bg-primary p-2 px-3 rounded">   {{ __(\App\Models\Support::$priority[$support->priority]) }}</span>
                                                @elseif($support->priority == 1)
                                                    <span data-toggle="tooltip" data-title="{{__('Priority')}}" class="text-capitalize status_badge badge bg-info p-2 px-3 rounded">   {{ __(\App\Models\Support::$priority[$support->priority]) }}</span>
                                                @elseif($support->priority == 2)
                                                    <span data-toggle="tooltip" data-title="{{__('Priority')}}" class="text-capitalize status_badge badge bg-warning p-2 px-3 rounded">   {{ __(\App\Models\Support::$priority[$support->priority]) }}</span>
                                                @elseif($support->priority == 3)
                                                    <span data-toggle="tooltip" data-title="{{__('Priority')}}" class="text-capitalize status_badge badge bg-danger p-2 px-3 rounded">   {{ __(\App\Models\Support::$priority[$support->priority]) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{$support->ticket_code}}</td>
                                    <td>
                                        @if(!empty($support->attachment))
                                            <a  class="bg-primary me-1 btn btn-sm align-items-center" href="{{ $supportpath . '/' . $support->attachment }}" download="" data-bs-toggle="tooltip" title="{{__('Download')}}">
                                                <i class="ti ti-download text-white"></i>
                                            </a>
                                            <a href="{{ $supportpath . '/' . $support->attachment }}"  class="bg-secondary btn btn-sm align-items-center" data-bs-toggle="tooltip" title="{{__('Preview')}}" target="_blank"><span class="btn-inner--icon"><i class="ti ti-crosshair text-white" ></i></span></a>
                                        @else
                                            -
                                        @endif

                                    </td>
                                    <td>{{!empty($support->assignUser)?$support->assignUser->name:'-'}}</td>
                                    <td>
                                        @if($support->status == 'Open')
                                            <span class="status_badge text-capitalize badge bg-success p-2 px-3 rounded">{{ __(\App\Models\Support::$status[$support->status]) }}</span>
                                        @elseif($support->status == 'Close')
                                            <span class="status_badge text-capitalize badge bg-danger p-2 px-3 rounded">{{ __(\App\Models\Support::$status[$support->status]) }}</span>
                                        @elseif($support->status == 'On Hold')
                                            <span  class="status_badge text-capitalize badge bg-warning p-2 px-3 rounded">{{ __(\App\Models\Support::$status[$support->status]) }}</span>
                                        @endif
                                    </td>
                                    <td>{{\Auth::user()->dateFormat($support->created_at)}}</td>
                                    <td class="Action">
                                    <span>
                                        <div class="action-btn me-2">
                                            <a href="{{ route('support.reply',\Crypt::encrypt($support->id)) }}" data-title="{{__('Support Reply')}}" class="mx-3 btn btn-sm align-items-center bg-warning" data-bs-toggle="tooltip" title="{{__('Reply')}}" data-original-title="{{__('Reply')}}">
                                                <i class="ti ti-corner-up-left text-white"></i>
                                            </a>
                                        </div>
                                        @if(\Auth::user()->type=='company' || \Auth::user()->id==$support->ticket_created)
                                            <div class="action-btn me-2">
                                                <a href="#" data-size="lg" data-url="{{ route('support.edit',$support->id) }}" data-ajax-popup="true" data-title="{{__('Edit Support')}}" class="mx-3 btn btn-sm align-items-center bg-info" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                            </div>
                                            <div class="action-btn">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['support.destroy', $support->id],'id'=>'delete-form-'.$support->id]) !!}
                                                    <a href="#!" class="mx-3 bg-danger btn btn-sm  align-items-center bs-pass-para" data-bs-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" title="{{__('Delete')}}" data-confirm-yes="document.getElementById('delete-form-{{$support->id}}').submit();">
                                                        <i class="ti ti-trash text-white"></i>
                                                    </a>
                                                 {!! Form::close() !!}
                                            </div>

                                        @endif
                                    </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

