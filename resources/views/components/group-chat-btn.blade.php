
@props(['item'])
<div class="relative"> <!-- Wrapper for each item -->
    <button class="w-full text-left justify-between p-4 hover:bg-gray-100 border-b flex items-center space-x-3"
        onclick="openChat({{ $item->id }})">
        <section class="flex align-middle items-center">
            <div class="w-10 h-10 bg-gray-300 rounded-full"></div>
            <div class="ms-3">
                <h3 class="font-medium group group-{{ $item->id }}">{{ $item->group_name }}</h3>
            </div>
        </section>

        <div onclick="event.stopPropagation();" data-dropdown-toggle="groupDropdown-{{ $item->id }}"
            class="h-7 w-7 grid place-items-center rounded-full bg-green-200 cursor-pointer">
            <i class="ri-more-2-line"></i>
        </div>
    </button>

    <!-- Dropdown OUTSIDE the button -->
    <div id="groupDropdown-{{ $item->id }}"
        class="absolute right-4 top-12 hidden z-[9999] bg-white divide-y divide-gray-100 rounded-lg shadow-lg w-44">
        <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownDefaultButton">
            <li><a href="#" onclick="if (confirm('Do you want to leave Channel?')) leaveChannel({{ $item->id }})" class="block px-4 py-2 hover:bg-gray-100">Leave Channel</a></li>
        </ul>
    </div>
</div>
