
@props(['data'])
<!-- Main modal -->
<div id="crypto-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Add Employee
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="crypto-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <form method="post" action="{{route('create.group')}}" class="p-4 md:p-5">
                @method('post')
                @csrf
                <input type="text" name="groupname" class="px-3 mb-4 py-2 rounded-sm w-full" required placeholder="Enter Group Name">
                @error('groupname')
                    <span class="text-red-500 text-sm font-bold">{{ $message }}</span>
                @enderror
                <div class="text-center w-full">
                    <small class="mt-4 text-yellow-50 text-center w-full">Select Users</small>
                </div>
                <hr>
                <ul class="my-4 space-y-3 group-modal overflow-y-auto">
                    @foreach ($data as $item)
                        <li>
                            <label for="{{$item->id}}" class="flex cursor-pointer items-center p-3 text-base font-bold text-gray-900 rounded-lg bg-gray-50 hover:bg-gray-100 group hover:shadow dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white">
                                <span class="flex-1 ms-3 whitespace-nowrap">{{ $item->name }}</span>
                                <input type="checkbox" name="users[]" value="{{$item->id}}" id="{{$item->id}}">
                            </label>
                        </li>
                    @endforeach
                    @error('users')
                        <span class="text-red-500 text-sm font-bold">{{ $message }}</span>
                    @enderror
                </ul>
                <div class="flex justify-center">
                   <button type="submit" class=" bg-purple-700 text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center">
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
