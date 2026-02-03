// ======================= GLOBAL STATE =======================
let calendar = null
let miniCalendar = null
let events = []
let currentEventId = null
let hiddenCategories = new Set()
let hiddenStatuses = new Set()
let selectedRoomFilter = ''
let pendingFormData = null

// ======================= CONSTANTS =======================
const STATUS_COLORS = {
  pending: '#f59e0b',
  approved: '#10b981',
  completed: '#6366f1',
  cancelled: '#ef4444'
}

const CATEGORY_COLORS = {
  work: '#bc307bff',
  seminar: '#c4b517ff',
  other: '#4d6d41ff'
}

const CATEGORY_NAMES = {
  work: 'Làm việc - nghiên cứu',
  seminar: 'Hội thảo - Seminar',
  other: 'Khác'
}

const STATUS_LABELS = {
  pending: 'Chờ duyệt',
  approved: 'Đã duyệt',
  completed: 'Đã hoàn thành',
  cancelled: 'Đã hủy'
}

const STATUS_ICONS = {
  pending: '<i class="fa-solid fa-clock"></i>',
  approved: '<i class="fa-solid fa-circle-check"></i>',
  completed: '<i class="fa-solid fa-check-double"></i>',
  cancelled: '<i class="fa-solid fa-ban"></i>'
}

const CATEGORY_ICONS = {
  work: '<i class="fa-solid fa-briefcase"></i>',
  seminar: '<i class="fa-solid fa-chalkboard-user"></i>',
  other: '<i class="fa-solid fa-ellipsis"></i>'
}

// ======================= DATA MAPS =======================
const roomMap = {}
const groupMap = {}

function initDataMaps() {
  if (window.LAB_ROOMS && Array.isArray(window.LAB_ROOMS)) {
    window.LAB_ROOMS.forEach((r) => {
      if (r.code != null) roomMap[String(r.code)] = r.name || String(r.code)
    })
  }

  if (window.LAB_GROUPS && Array.isArray(window.LAB_GROUPS)) {
    window.LAB_GROUPS.forEach((g) => {
      if (g.id != null) groupMap[String(g.id)] = g.name || String(g.id)
    })
  }
}

// ======================= UTILITY FUNCTIONS =======================
function normalizeDateString(v) {
  if (!v) return null
  if (v instanceof Date) return v
  if (typeof v !== 'string') return v
  return v.includes('T') ? v : v.replace(' ', 'T')
}

function hexToRgb(color) {
  if (!color || typeof color !== 'string') return null
  let c = color.trim()
  if (c.startsWith('rgb')) return null
  if (c[0] === '#') c = c.slice(1)
  if (c.length === 3) c = c.split('').map((x) => x + x).join('')
  if (c.length === 8) c = c.slice(0, 6)
  if (c.length !== 6) return null

  const r = parseInt(c.slice(0, 2), 16)
  const g = parseInt(c.slice(2, 4), 16)
  const b = parseInt(c.slice(4, 6), 16)

  if ([r, g, b].some((n) => Number.isNaN(n))) return null
  return { r, g, b }
}

function isLightColor(color) {
  const rgb = hexToRgb(color)
  if (!rgb) return false
  const { r, g, b } = rgb
  const brightness = (r * 299 + g * 587 + b * 114) / 1000
  return brightness > 155
}

function readableTextColor(bg) {
  return isLightColor(bg) ? '#1f2937' : '#ffffff'
}

function formatDateTime(dateString) {
  const date = dateString instanceof Date ? dateString : new Date(dateString)
  const day = String(date.getDate()).padStart(2, '0')
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const year = date.getFullYear()
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')
  return `${day}/${month}/${year} ${hours}:${minutes}`
}

function formatLocalDateTime(date) {
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  const hour = String(date.getHours()).padStart(2, '0')
  const minute = String(date.getMinutes()).padStart(2, '0')
  const second = String(date.getSeconds()).padStart(2, '0')
  return `${year}-${month}-${day} ${hour}:${minute}:${second}`
}

function showToast(type, message) {
  if (window.toastr) {
    toastr[type](message)
  }
}

// ======================= MODAL MANAGEMENT =======================
function toggleModal(modalId, show) {
  const modal = document.getElementById(modalId)
  if (!modal) return

  if (show) {
    modal.classList.add('active')
  } else {
    modal.classList.remove('active')
  }
}

window.closeModal = function() {
  toggleModal('eventModal', false)
}

window.closeDetailModal = function() {
  toggleModal('detailModal', false)
}

window.closeConfirmDelete = function() {
  toggleModal('confirmDeleteModal', false)
}

window.closeConflictModal = function() {
  toggleModal('confirmConflictModal', false)
  pendingFormData = null
}

function openCreateModal(start = null, end = null) {
  const form = document.getElementById('eventForm')
  if (form) form.reset()

  document.getElementById('modalTitle').textContent = 'Tạo sự kiện mới'
  document.getElementById('eventId').value = ''

  const now = new Date()
  const today = now.toISOString().split('T')[0]

  const startDateInput = document.getElementById('eventStartDate')
  const startTimeInput = document.getElementById('eventStartTime')
  const endTimeInput = document.getElementById('eventEndTime')

  if (start) {
    const startDate = new Date(start)
    if (startDate < now) {
      showToast('warning', 'Không thể tạo sự kiện trong quá khứ')
      return
    }

    if (startDateInput) startDateInput.value = startDate.toISOString().split('T')[0]
    if (startTimeInput) startTimeInput.value = startDate.toTimeString().slice(0, 5)

    if (end) {
      const endDate = new Date(end)
      if (endTimeInput) endTimeInput.value = endDate.toTimeString().slice(0, 5)
    } else {
      const endAuto = new Date(startDate.getTime() + 60 * 60 * 1000)
      if (endTimeInput) endTimeInput.value = endAuto.toTimeString().slice(0, 5)
    }
  } else {
    if (startDateInput) startDateInput.value = today
    if (startTimeInput) startTimeInput.value = '09:00'
    if (endTimeInput) endTimeInput.value = '10:00'
  }

  // Reset repeat controls
  const repeatTypeSelect = document.getElementById('eventRepeatType')
  const repeatUntilInput = document.getElementById('eventRepeatUntil')
  const weekdaySection = document.getElementById('weekdaySection')
  const weekSummary = document.getElementById('weekSummary')

  if (repeatTypeSelect) repeatTypeSelect.value = ''
  if (repeatUntilInput) repeatUntilInput.value = ''
  if (weekdaySection) {
    weekdaySection.style.display = 'none'
    document.querySelectorAll('.weekday-checkbox').forEach((cb) => {
      cb.checked = false
    })
  }
  if (weekSummary) weekSummary.textContent = ''

  toggleModal('eventModal', true)
  buildOccurrencesFromForm({ preview: true })
}

// ======================= PERMISSION CHECK =======================
function checkPermission(eventData) {
  if (!window.LAB_USER || !window.LAB_USER.logged_in) return false

  const u = window.LAB_USER
  const isAdmin = u.is_admin === true || String(u.role_id) === '1' || String(u.roleId || '') === '1'

  if (isAdmin) return true

  const props = eventData.extendedProps || {}
  const isOwner = props.user_id != null && String(props.user_id) === String(u.user_id)
  if (!isOwner) return false

  if (props.status === 'approved') {
    const eventStart = new Date(eventData.start)
    const now = new Date()
    if (eventStart <= now) return false
  }

  return true
}

// ======================= EVENT RENDERING =======================
 
 function getEventContent(arg) {
    const event = arg.event
    const props = event.extendedProps || {}
    const status = props.status || 'pending'
    const category = props.category || 'work'

    const durationMinutes = (event.end - event.start) / (1000 * 60)

    const isTiny = durationMinutes <= 30
    const isShort = durationMinutes <= 60
    const isMedium = durationMinutes <= 90

     const categoryIcon = props.categoryIcon
        ? `<i class="fa-solid fa-${props.categoryIcon}"></i>`
        : '<i class="fa-solid fa-briefcase"></i>' // fallback

    const statusIcon = STATUS_ICONS[status] || STATUS_ICONS.pending
    const color = props._color || event.backgroundColor || '#3788d8'
    const chipBg = isLightColor(color) ? 'rgba(7, 7, 8, 0.12)' : 'rgba(48, 46, 46, 0.22)'
    const chipBorder = isLightColor(color) ? 'rgba(5, 5, 5, 0.16)' : 'rgba(28, 27, 27, 0.18)'

     const isCompleted = status === 'completed'
    const textOpacity = isCompleted ? '0.5' : '0.7'
    const iconOpacity = isCompleted ? '0.4' : '0.7'

    let html = ''

    if (isTiny) {
        html = `
      <div class="fc-event-main-custom fc-event-tiny" style="padding:4px 6px;overflow:hidden;height:100%;display:flex;align-items:center;gap:6px;color:rgba(100,100,100,${textOpacity});">
        <div style="font-weight:600;font-size:10px;white-space:nowrap;">${arg.timeText || ''}</div>
        ${categoryIcon}
        <div style="font-weight:600;font-size:10px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;">${event.title || ''}</div>
      </div>
    `
    } else if (isShort) {
        html = `
      <div class="fc-event-main-custom fc-event-short" style="padding:5px 7px;overflow:hidden;height:100%;color:rgba(100,100,100,${textOpacity});">
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:3px;">
          <div style="font-weight:600;font-size:10px;letter-spacing:.2px;">${arg.timeText || ''}</div>
          <span style="margin-left:auto;display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:999px;background:${chipBg};border:1px solid ${chipBorder};font-size:9px;opacity:${iconOpacity};">${statusIcon}</span>
        </div>
        <div style="display:flex;align-items:center;gap:5px;">
          ${categoryIcon}
          <div style="font-weight:700;line-height:1.2;font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;">${event.title || ''}</div>
        </div>
      </div>
    `
    } else if (isMedium) {
        html = `
      <div class="fc-event-main-custom fc-event-medium" style="padding:6px 8px;overflow:hidden;height:100%;color:rgba(100,100,100,${textOpacity});">
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:3px;">
          <div style="font-weight:600;font-size:11px;letter-spacing:.2px;">${arg.timeText || ''}</div>
          <span style="margin-left:auto;display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:999px;background:${chipBg};border:1px solid ${chipBorder};font-size:10px;opacity:${iconOpacity};">${statusIcon}</span>
        </div>
        <div style="display:flex;align-items:center;gap:5px;">
          ${categoryIcon}
          <div style="font-weight:700;line-height:1.2;font-size:14px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;">${event.title || ''}</div>
        </div>
      </div>
    `
    } else {
        html = `
      <div class="fc-event-main-custom fc-event-full" style="padding:6px 8px;overflow:hidden;height:100%;color:rgba(100,100,100,${textOpacity});">
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
          <div class="fc-event-time" style="font-weight:600;letter-spacing:.2px;font-size:11px;">${arg.timeText || ''}</div>
          <span style="margin-left:auto;display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:999px;background:${chipBg};border:1px solid ${chipBorder};font-size:11px;opacity:${iconOpacity};">${statusIcon}</span>
        </div>
        <div style="display:flex;align-items:center;gap:6px;">
          ${categoryIcon}
          <div class="fc-event-title" style="font-weight:700;line-height:1.2;font-size:15px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;flex:1;">${event.title || ''}</div>
        </div>
      </div>
    `
    }

    return { html }
}

function onEventDidMount(info) {
  const el = info.el
  const props = info.event.extendedProps || {}
  const status = props.status || 'pending'
  const color = props._color || info.event.backgroundColor || '#3788d8'
  const textColor = props._textColor || readableTextColor(color)

  el.style.setProperty('--fc-event-bg-color', color)
  el.style.setProperty('--fc-event-border-color', color)
  el.style.setProperty('--fc-event-text-color', textColor)

  if (status === 'pending') {
    el.classList.add('is-pending')
  } else {
    el.classList.remove('is-pending')
  }

  const canEdit = checkPermission(info.event)
  if (!canEdit) {
    el.classList.add('is-no-edit')
  } else {
    el.classList.remove('is-no-edit')
  }
}


function onEventDidMount(info) {
  const el = info.el
  const props = info.event.extendedProps || {}
  const status = props.status || 'pending'
  const color = props._color || info.event.backgroundColor || '#3788d8'
  const textColor = props._textColor || readableTextColor(color)

  el.style.setProperty('--fc-event-bg-color', color)
  el.style.setProperty('--fc-event-border-color', color)
  el.style.setProperty('--fc-event-text-color', textColor)

  if (status === 'pending') {
    el.classList.add('is-pending')
  } else {
    el.classList.remove('is-pending')
  }

  const canEdit = checkPermission(info.event)
  if (!canEdit) {
    el.classList.add('is-no-edit')
  } else {
    el.classList.remove('is-no-edit')
  }
}

// ======================= CALENDAR INITIALIZATION =======================
function initMiniCalendar() {
  const miniEl = document.getElementById('miniCalendar')
  if (!miniEl) return

  miniCalendar = new FullCalendar.Calendar(miniEl, {
    initialView: 'dayGridMonth',
    locale: 'vi',
    firstDay: 0,
    headerToolbar: {
      left: 'prev',
      center: 'title',
      right: 'next'
    },
    buttonText: {
      prev: '‹',
      next: '›'
    },
    height: 'auto',
    contentHeight: 'auto',
    expandRows: true,
    fixedWeekCount: false,
    showNonCurrentDates: true,
    selectable: false,
    dayMaxEvents: false,
    navLinks: false,
    dateClick: function (info) {
      if (calendar) calendar.gotoDate(info.date)
    }
  })

  miniCalendar.render()
}

function initCalendar() {
  initMiniCalendar()

  const calendarEl = document.getElementById('calendar')
  if (!calendarEl) return

  const canCreate = window.LAB_USER && window.LAB_USER.logged_in

  calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'timeGridWeek',
    locale: 'vi',
    firstDay: 1,
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    buttonText: {
      today: 'Hôm nay',
      month: 'Tháng',
      week: 'Tuần',
      day: 'Ngày'
    },
    slotMinTime: '07:00:00',
    slotMaxTime: '19:00:00',
    allDaySlot: false,
    nowIndicator: true,
    editable: true,
    selectMirror: true,
    selectable: canCreate,
    dayMaxEvents: true,
    weekends: true,
    height: 'auto',
    selectConstraint: {
      start: new Date().toISOString().split('T')[0]
    },

    eventAllow: function (dropInfo, draggedEvent) {
      if (!checkPermission(draggedEvent)) return false
      const now = new Date()
      if (dropInfo.start && dropInfo.start < now) return false

      // ✅ NGĂN KÉO TRÙNG: Kiểm tra xem có event nào khác trùng thời gian không
      const hasOverlap = calendar.getEvents().some(event => {
        if (event.id === draggedEvent.id) return false // Bỏ qua chính nó
        if (event.extendedProps.status === 'cancelled') return false // Bỏ qua lịch đã hủy

        const eventStart = event.start
        const eventEnd = event.end || new Date(eventStart.getTime() + 60 * 60 * 1000)
        const dropEnd = dropInfo.end || new Date(dropInfo.start.getTime() + 60 * 60 * 1000)

        // Kiểm tra overlap: start1 < end2 && end1 > start2
        return dropInfo.start < eventEnd && dropEnd > eventStart
      })

      if (hasOverlap) {
        showToast('warning', 'Không thể kéo vào thời gian đã có lịch khác')
        return false
      }

      return true
    },

    eventDidMount: onEventDidMount,
    eventContent: getEventContent,

    eventClick: function (info) {
      showEventDetails(info.event)
    },

    select: function (info) {
      const now = new Date()
      if (info.start < now) {
        showToast('warning', 'Không thể đăng ký sự kiện trong quá khứ')
        calendar.unselect()
        return
      }
      openCreateModal(info.start, info.end)
    },

    eventDrop: function (info) {
      const now = new Date()
      if (info.event.start < now) {
        showToast('warning', 'Không thể chuyển sự kiện vào quá khứ')
        info.revert()
        return
      }
      updateEventTime(info.event, info)
    },

    eventResize: function (info) {
      const now = new Date()
      if (info.event.start < now) {
        showToast('warning', 'Không thể chuyển sự kiện vào quá khứ')
        info.revert()
        return
      }
      updateEventTime(info.event, info)
    }
  })

  initFilters()
  initButtons()
  setMinDateForInputs()
  initRepeatControls()
  loadEvents()
  calendar.render()
}

// ======================= FILTERS =======================
function initFilters() {
  const statusCheckboxes = document.querySelectorAll('[data-filter-status]')
  statusCheckboxes.forEach((cb) => {
    const status = cb.getAttribute('data-filter-status')
    if (!cb.checked) hiddenStatuses.add(status)

    cb.addEventListener('change', function () {
      if (this.checked) hiddenStatuses.delete(status)
      else hiddenStatuses.add(status)
      updateCalendar()
    })
  })

  const categoryCheckboxes = document.querySelectorAll('[data-filter-category]')
  categoryCheckboxes.forEach((cb) => {
    const cat = cb.getAttribute('data-filter-category')
    if (!cb.checked) hiddenCategories.add(cat)

    cb.addEventListener('change', function () {
      if (this.checked) hiddenCategories.delete(cat)
      else hiddenCategories.add(cat)
      updateCalendar()
    })
  })

  const roomFilterSelect = document.getElementById('labRoomFilter')
  if (roomFilterSelect) {
    roomFilterSelect.addEventListener('change', function () {
      selectedRoomFilter = this.value || ''
      updateCalendar()
    })
  }
}

function initButtons() {
  const createBtn = document.querySelector('.js-open-create-event')
  if (createBtn) {
    createBtn.addEventListener('click', function () {
      openCreateModal()
    })
  }
}

function setMinDateForInputs() {
  const today = new Date().toISOString().split('T')[0]
  const inputs = ['eventStartDate', 'eventEndDate', 'eventRepeatUntil']
  inputs.forEach(id => {
    const input = document.getElementById(id)
    if (input) input.setAttribute('min', today)
  })
}

function initRepeatControls() {
  const repeatTypeSelect = document.getElementById('eventRepeatType')
  const weekdaySection = document.getElementById('weekdaySection')
  const weekSummary = document.getElementById('weekSummary')

  if (!repeatTypeSelect || !weekdaySection) return

  repeatTypeSelect.addEventListener('change', function () {
    if (this.value === 'weekly') {
      weekdaySection.style.display = 'block'
    } else {
      weekdaySection.style.display = 'none'
      document.querySelectorAll('.weekday-checkbox').forEach((cb) => {
        cb.checked = false
      })
      if (weekSummary) weekSummary.textContent = ''
    }
    buildOccurrencesFromForm({ preview: true })
  })

  const repeatUntilInput = document.getElementById('eventRepeatUntil')
  if (repeatUntilInput) {
    repeatUntilInput.addEventListener('change', () => buildOccurrencesFromForm({ preview: true }))
  }

  document.querySelectorAll('.weekday-checkbox').forEach((cb) => {
    cb.addEventListener('change', () => buildOccurrencesFromForm({ preview: true }))
  })

  const inputs = ['eventStartDate', 'eventStartTime', 'eventEndTime']
  inputs.forEach(id => {
    const el = document.getElementById(id)
    if (el) el.addEventListener('change', () => buildOccurrencesFromForm({ preview: true }))
  })
}

// ======================= RECURRING LOGIC =======================
function buildOccurrencesFromForm(options = {}) {
  const { preview = false, maxOccurrences = 500 } = options

  const startDateStr = document.getElementById('eventStartDate')?.value || ''
  const startTimeStr = document.getElementById('eventStartTime')?.value || ''
  const endTimeStr = document.getElementById('eventEndTime')?.value || ''
  const repeatType = document.getElementById('eventRepeatType')?.value || ''
  const repeatUntilStr = document.getElementById('eventRepeatUntil')?.value || ''

  const weekdayValues = Array.from(document.querySelectorAll('.weekday-checkbox:checked'))
    .map((cb) => parseInt(cb.value, 10))

  const weekSummaryEl = document.getElementById('weekSummary')
  if (weekSummaryEl && preview) weekSummaryEl.textContent = ''

  if (!startDateStr || !startTimeStr || !endTimeStr) {
    return { ok: false, message: 'Thiếu ngày/giờ.' }
  }

  const start = new Date(`${startDateStr}T${startTimeStr}:00`)
  const end = new Date(`${startDateStr}T${endTimeStr}:00`)

  if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime())) {
    if (!preview) showToast('error', 'Ngày/giờ không hợp lệ.')
    return { ok: false }
  }

  if (end <= start) {
    if (!preview) showToast('error', 'Giờ kết thúc phải sau giờ bắt đầu.')
    return { ok: false }
  }

  const baseDurationMs = end.getTime() - start.getTime()

  if (!repeatType || !repeatUntilStr) {
    if (preview && weekSummaryEl) {
      weekSummaryEl.textContent = '(Sẽ tạo 1 lịch)'
    }
    return {
      ok: true,
      occurrences: [{ start: new Date(start), end: new Date(end) }]
    }
  }

  const repeatUntil = new Date(repeatUntilStr + 'T23:59:59')
  if (Number.isNaN(repeatUntil.getTime())) {
    if (!preview) showToast('error', 'Ngày lặp đến không hợp lệ.')
    return { ok: false }
  }

  if (repeatUntil < start) {
    if (!preview) showToast('error', 'Ngày lặp đến phải sau hoặc bằng ngày bắt đầu.')
    return { ok: false }
  }

  const occ = []

  if (repeatType === 'daily') {
    let cursor = new Date(start)
    while (cursor <= repeatUntil && occ.length < maxOccurrences) {
      const s = new Date(cursor)
      const e = new Date(cursor.getTime() + baseDurationMs)
      occ.push({ start: s, end: e })
      cursor.setDate(cursor.getDate() + 1)
    }
  } else if (repeatType === 'weekly') {
    if (!weekdayValues.length) {
      if (!preview) showToast('error', 'Hãy chọn ít nhất 1 ngày trong tuần để lặp.')
      return { ok: false }
    }

    let cursor = new Date(start)
    cursor.setHours(0, 0, 0, 0)

    while (cursor <= repeatUntil && occ.length < maxOccurrences) {
      const dow = cursor.getDay()
      if (weekdayValues.includes(dow)) {
        const s = new Date(
          cursor.getFullYear(),
          cursor.getMonth(),
          cursor.getDate(),
          start.getHours(),
          start.getMinutes(),
          start.getSeconds()
        )
        const e = new Date(s.getTime() + baseDurationMs)
        occ.push({ start: s, end: e })
      }
      cursor.setDate(cursor.getDate() + 1)
    }
  } else if (repeatType === 'monthly') {
    let cursor = new Date(start)
    while (cursor <= repeatUntil && occ.length < maxOccurrences) {
      const s = new Date(cursor)
      const e = new Date(cursor.getTime() + baseDurationMs)
      occ.push({ start: s, end: e })
      cursor.setMonth(cursor.getMonth() + 1)
    }
  }

  if (!occ.length) {
    if (!preview) showToast('error', 'Không tạo được lịch nào, vui lòng kiểm tra lại phần lặp.')
    return { ok: false }
  }

  if (preview && weekSummaryEl) {
    weekSummaryEl.textContent = `(Sẽ tạo ${occ.length} lịch)`
  }

  return { ok: true, occurrences: occ }
}

// ======================= LOAD & UPDATE EVENTS =======================
// async function loadEvents() {
//   try {
//     const response = await fetch('/bookings', { headers: { Accept: 'application/json' } })
//     const text = await response.text()

//     let data
//     try {
//       data = JSON.parse(text)
//     } catch (e) {
//       console.error('loadEvents parse error:', response.status, text)
//       showToast('error', 'Không tải được dữ liệu lịch (response không phải JSON).')
//       return
//     }

//     if (!response.ok) {
//       console.error('loadEvents not ok:', response.status, data)
//       showToast('error', data?.message || 'Không tải được dữ liệu lịch.')
//       return
//     }

//     const raw = Array.isArray(data) ? data : data.data || []

//     events = raw.map((item) => {
//       const category = item.category || 'work'
//       const status = item.status || 'pending'
//       const safeCategory = CATEGORY_COLORS[category] ? category : 'work'

//       const roomCode = item.lab_code != null ? String(item.lab_code) : null
//       const roomName = roomCode ? roomMap[roomCode] || roomCode : null

//       const bgColor = STATUS_COLORS[status] || CATEGORY_COLORS[safeCategory] || '#3788d8'

//       const registeredFor = item.registered_for != null ? String(item.registered_for) : ''
//       const registeredForName = registeredFor ? groupMap[registeredFor] || registeredFor : ''

//       return {
//         id: item.id,
//         title: item.title,
//         start: normalizeDateString(item.start),
//         end: normalizeDateString(item.end),
//         category: safeCategory,
//         description: item.description,
//         status,
//         roomCode,
//         roomName,
//         color: bgColor,
//         lab_code: roomCode,
//         user_id: item.user_id || item.userId || null,
//         registered_for: registeredFor,
//         registeredForName
//       }
//     })

//     updateCalendar()
//   } catch (err) {
//     console.error('loadEvents error:', err)
//     showToast('error', 'Không tải được dữ liệu lịch.')
//   }
// }
async function loadEvents() {
  try {
    const response = await fetch('/bookings', { headers: { Accept: 'application/json' } })
    const text = await response.text()

    let data
    try {
      data = JSON.parse(text)
    } catch (e) {
      console.error('loadEvents parse error:', response.status, text)
      showToast('error', 'Không tải được dữ liệu lịch (response không phải JSON).')
      return
    }

    if (!response.ok) {
      console.error('loadEvents not ok:', response.status, data)
      showToast('error', data?.message || 'Không tải được dữ liệu lịch.')
      return
    }

    const raw = Array.isArray(data) ? data : data.data || []

    events = raw.map((item) => {
      const roomCode = item.lab_code != null ? String(item.lab_code) : null
      const roomName = roomCode ? roomMap[roomCode] || roomCode : null

       const bgColor = item.color || '#3788d8'

      const registeredFor = item.registered_for != null ? String(item.registered_for) : ''
      const registeredForName = registeredFor ? groupMap[registeredFor] || registeredFor : ''

      return {
        id: item.id,
        title: item.title,
        start: normalizeDateString(item.start),
        end: normalizeDateString(item.end),
        category: item.category,
        categoryIcon: item.category_icon,
        categoryName: item.category_name,
        statusName: item.status_name,
        description: item.description,
        status: item.status,
        roomCode,
        roomName,
        color: bgColor,
        lab_code: roomCode,
        user_id: item.user_id || item.userId || null,
        registered_for: registeredFor,
        registeredForName
      }
    })

    updateCalendar()
  } catch (err) {
    console.error('loadEvents error:', err)
    showToast('error', 'Không tải được dữ liệu lịch.')
  }
}
function updateCalendar() {
  if (!calendar) return

  calendar.batchRendering(() => {
    calendar.removeAllEvents()

    const visibleEvents = events.filter(
      (e) =>
        !hiddenCategories.has(e.category) &&
        !hiddenStatuses.has(e.status) &&
        (!selectedRoomFilter || e.roomCode === selectedRoomFilter)
    )

    visibleEvents.forEach((e) => {
      const startDate = e.start instanceof Date ? e.start : new Date(e.start)
      const endDate = e.end
        ? (e.end instanceof Date ? e.end : new Date(e.end))
        : new Date(startDate.getTime() + 60 * 60 * 1000)

      const bg = e.color || '#3788d8' // ✅ Màu từ EventStatus
      const tx = readableTextColor(bg)

      calendar.addEvent({
        id: e.id,
        title: e.title,
        start: startDate,
        end: endDate,
        backgroundColor: bg,
        borderColor: bg,
        textColor: tx,
        extendedProps: {
          category: e.category,
          categoryIcon: e.categoryIcon,
          categoryName: e.categoryName,
          statusName: e.statusName,
          description: e.description,
          status: e.status,
          roomCode: e.roomCode,
          roomName: e.roomName,
          user_id: e.user_id,
          registered_for: e.registered_for,
          registeredForName: e.registeredForName,
          _color: bg,
          _textColor: tx
        }
      })
    })
  })
}
// function updateCalendar() {
//   if (!calendar) return

//   calendar.batchRendering(() => {
//     calendar.removeAllEvents()

//     const visibleEvents = events.filter(
//       (e) =>
//         !hiddenCategories.has(e.category) &&
//         !hiddenStatuses.has(e.status) &&
//         (!selectedRoomFilter || e.roomCode === selectedRoomFilter)
//     )

//     visibleEvents.forEach((e) => {
//       const startDate = e.start instanceof Date ? e.start : new Date(e.start)
//       const endDate = e.end
//         ? (e.end instanceof Date ? e.end : new Date(e.end))
//         : new Date(startDate.getTime() + 60 * 60 * 1000)

//       const bg = STATUS_COLORS[e.status] || e.color || CATEGORY_COLORS[e.category] || '#3788d8'
//       const tx = readableTextColor(bg)

//       calendar.addEvent({
//         id: e.id,
//         title: e.title,
//         start: startDate,
//         end: endDate,
//         backgroundColor: bg,
//         borderColor: bg,
//         textColor: tx,
//         extendedProps: {
//           category: e.category,
//           description: e.description,
//           status: e.status,
//           roomCode: e.roomCode,
//           roomName: e.roomName,
//           user_id: e.user_id,
//           registered_for: e.registered_for,
//           registeredForName: e.registeredForName,
//           _color: bg,
//           _textColor: tx
//         }
//       })
//     })
//   })
// }

// ======================= SAVE EVENT =======================
async function sendBookingRequest(url, formData) {
  const response = await fetch(url, {
    method: 'POST',
    body: formData,
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      Accept: 'application/json'
    }
  })

  const text = await response.text()
  const contentType = response.headers.get('content-type') || ''

  if (!contentType.includes('application/json')) {
    console.error('sendBookingRequest not JSON:', response.status, text)
    showToast('error', 'Có lỗi xảy ra khi lưu sự kiện (server không trả JSON).')
    return { ok: false }
  }

  let data
  try {
    data = JSON.parse(text)
  } catch (e) {
    console.error('sendBookingRequest parse error:', response.status, text)
    showToast('error', 'Có lỗi xảy ra khi lưu sự kiện (response không phải JSON).')
    return { ok: false }
  }

  if (!response.ok) {
    showToast('error', data?.message || 'Có lỗi xảy ra khi lưu sự kiện.')
    return { ok: false }
  }

  return { ok: true, data }
}

window.saveEvent = async function() {
  const eventId = document.getElementById('eventId').value
  const title = document.getElementById('eventTitle').value.trim()
  const category = document.getElementById('eventCategory').value
  const labCode = "LAB-304"
  const registeredFor = document.getElementById('eventRegisteredFor').value.trim()

  const startDate = document.getElementById('eventStartDate').value
  const startTime = document.getElementById('eventStartTime').value
  const endTime = document.getElementById('eventEndTime').value
  const description = document.getElementById('eventDescription').value.trim()

  const repeatType = document.getElementById('eventRepeatType').value
  const repeatUntil = document.getElementById('eventRepeatUntil').value
  const repeatDays = Array.from(document.querySelectorAll('.weekday-checkbox:checked')).map((cb) => cb.value)

  if (!title || !labCode || !startDate || !startTime || !endTime) {
    showToast('error', 'Vui lòng điền đầy đủ thông tin bắt buộc.')
    return
  }

  const occResult = buildOccurrencesFromForm({ preview: false, maxOccurrences: 500 })
  if (!occResult.ok) return

  const occurrences = occResult.occurrences || []
  if (!occurrences.length) {
    showToast('error', 'Không tạo được lịch nào, vui lòng kiểm tra lại phần lặp.')
    return
  }

  const isEditing = !!eventId
  const baseUrl = isEditing ? `/bookings/${eventId}` : '/bookings'

  try {
    if (isEditing) {
      const start = `${startDate} ${startTime}:00`
      const end = `${startDate} ${endTime}:00`

      const fd = new FormData()
      fd.append('title', title)
      fd.append('category', category)
      fd.append('lab_code', labCode)
      fd.append('start', start)
      fd.append('end', end)
      fd.append('description', description)
      fd.append('registered_for', registeredFor)
      fd.append('_method', 'PUT')

      fd.append('repeat_type', repeatType || '')
      fd.append('repeat_until', repeatUntil || '')
      repeatDays.forEach((d) => fd.append('repeat_days[]', d))

      const filesEl = document.getElementById('eventFiles')
      if (filesEl?.files.length > 0) {
        for (let i = 0; i < filesEl.files.length; i++) {
          fd.append('files[]', filesEl.files[i])
        }
      }

      const res = await sendBookingRequest(baseUrl, fd)
      if (!res.ok) return

      if (res.data?.message) showToast('success', res.data.message)

      closeModal()
      await loadEvents()
    } else {
      const fd = new FormData()
      fd.append('title', title)
      fd.append('category', category)
      fd.append('lab_code', labCode)
      fd.append('description', description)
      fd.append('registered_for', registeredFor)
      fd.append('is_recurring', repeatType ? 'true' : 'false')

      occurrences.forEach((occ, index) => {
        const toStr = (d) =>
          `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')} ${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}:00`

        fd.append(`occurrences[${index}][start]`, toStr(occ.start))
        fd.append(`occurrences[${index}][end]`, toStr(occ.end))
      })

      const filesEl = document.getElementById('eventFiles')
      if (filesEl?.files.length > 0) {
        for (let i = 0; i < filesEl.files.length; i++) {
          fd.append('files[]', filesEl.files[i])
        }
      }

      const res = await sendBookingRequest('/bookings', fd)
      if (!res.ok) return

      if (res.data?.type === 'confirm') {
        // Lưu formData để dùng lại khi user confirm
        pendingFormData = fd
        showConflictModal(res.data.data.conflicts)
        return
      }

      if (res.data?.type === 'error') {
        showToast('error', res.data.message)
        return
      }

      showToast('success', res.data?.message || 'Đã gửi yêu cầu đăng ký lịch.')
      closeModal()
      await loadEvents()
    }
  } catch (error) {
    console.error('saveEvent error:', error)
    showToast('error', 'Có lỗi xảy ra khi lưu sự kiện.')
  }
}

// ======================= EVENT DETAILS =======================
// function showEventDetails(eventData) {
//   currentEventId = eventData.id

//   document.getElementById('detailTitle').textContent = eventData.title
//   document.getElementById('detailTime').textContent =
//     `${formatDateTime(eventData.start)} - ${formatDateTime(eventData.end)}`
//   document.getElementById('detailRoom').textContent = eventData.extendedProps.roomName || ''

//   const registeredForRow = document.getElementById('detailRegisteredForRow')
//   const registeredForSpan = document.getElementById('detailRegisteredFor')
//   const rfName = eventData.extendedProps.registeredForName || ''

//   if (rfName) {
//     registeredForSpan.textContent = rfName
//     registeredForRow.style.display = 'flex'
//   } else {
//     registeredForRow.style.display = 'none'
//   }

//   const descRow = document.getElementById('detailDescriptionRow')
//   const descSpan = document.getElementById('detailDescription')

//   if (eventData.extendedProps.description) {
//     descSpan.textContent = eventData.extendedProps.description
//     descRow.style.display = 'flex'
//   } else {
//     descRow.style.display = 'none'
//   }

//   document.getElementById('detailCategory').textContent =
//     CATEGORY_NAMES[eventData.extendedProps.category] || eventData.extendedProps.category

//   const status = eventData.extendedProps.status || 'pending'
//   document.getElementById('detailStatus').textContent = STATUS_LABELS[status] || status

//   ;['pending', 'approved', 'completed', 'cancelled'].forEach(s => {
//     const icon = document.getElementById(`status${s.charAt(0).toUpperCase() + s.slice(1)}Icon`)
//     if (icon) icon.style.display = status === s ? 'inline' : 'none'
//   })

//   const canEdit = checkPermission(eventData)
//   const editBtn = document.getElementById('editEventBtn')
//   const deleteBtn = document.getElementById('deleteEventBtn')

//   const now = new Date()
//   const eventStart = new Date(eventData.start)
//   const eventEnd = eventData.end ? new Date(eventData.end) : eventStart

//   const isCompleted = status === 'completed'
//   const isOngoing = now >= eventStart && now <= eventEnd && status === 'approved'

//   const shouldHideButtons = !canEdit || isCompleted || isOngoing

//   if (editBtn) editBtn.style.display = shouldHideButtons ? 'none' : 'inline-flex'
//   if (deleteBtn) deleteBtn.style.display = shouldHideButtons ? 'none' : 'inline-flex'

//   toggleModal('detailModal', true)
// }
function showEventDetails(eventData) {
  currentEventId = eventData.id

  document.getElementById('detailTitle').textContent = eventData.title
  document.getElementById('detailTime').textContent =
    `${formatDateTime(eventData.start)} - ${formatDateTime(eventData.end)}`
  document.getElementById('detailRoom').textContent = eventData.extendedProps.roomName || ''

  const registeredForRow = document.getElementById('detailRegisteredForRow')
  const registeredForSpan = document.getElementById('detailRegisteredFor')
  const rfName = eventData.extendedProps.registeredForName || ''

  if (rfName) {
    registeredForSpan.textContent = rfName
    registeredForRow.style.display = 'flex'
  } else {
    registeredForRow.style.display = 'none'
  }

  const descRow = document.getElementById('detailDescriptionRow')
  const descSpan = document.getElementById('detailDescription')

  if (eventData.extendedProps.description) {
    descSpan.textContent = eventData.extendedProps.description
    descRow.style.display = 'flex'
  } else {
    descRow.style.display = 'none'
  }

   document.getElementById('detailCategory').textContent =
    eventData.extendedProps.categoryName || CATEGORY_NAMES[eventData.extendedProps.category] || eventData.extendedProps.category

  const status = eventData.extendedProps.status || 'pending'
   document.getElementById('detailStatus').textContent =
    eventData.extendedProps.statusName || STATUS_LABELS[status] || status

  ;['pending', 'approved', 'completed', 'cancelled'].forEach(s => {
    const icon = document.getElementById(`status${s.charAt(0).toUpperCase() + s.slice(1)}Icon`)
    if (icon) icon.style.display = status === s ? 'inline' : 'none'
  })

  const canEdit = checkPermission(eventData)
  const editBtn = document.getElementById('editEventBtn')
  const deleteBtn = document.getElementById('deleteEventBtn')

  const now = new Date()
  const eventStart = new Date(eventData.start)
  const eventEnd = eventData.end ? new Date(eventData.end) : eventStart

  const isCompleted = status === 'completed'
  const isOngoing = now >= eventStart && now <= eventEnd && status === 'approved'

  const shouldHideButtons = !canEdit || isCompleted || isOngoing

  if (editBtn) editBtn.style.display = shouldHideButtons ? 'none' : 'inline-flex'
  if (deleteBtn) deleteBtn.style.display = shouldHideButtons ? 'none' : 'inline-flex'

  toggleModal('detailModal', true)
}
window.editEvent = function() {
  const eventData = calendar.getEventById(currentEventId)
  if (!eventData) {
    console.error('Event not found:', currentEventId)
    return
  }

  if (!checkPermission(eventData)) {
    showToast('error', 'Bạn không có quyền chỉnh sửa sự kiện này.')
    return
  }

  closeDetailModal()

  setTimeout(() => {
    const form = document.getElementById('eventForm')
    if (form) form.reset()

    document.getElementById('modalTitle').textContent = 'Chỉnh sửa sự kiện'
    document.getElementById('eventId').value = currentEventId
    document.getElementById('eventTitle').value = eventData.title
    document.getElementById('eventCategory').value = eventData.extendedProps.category || 'work'
    document.getElementById('eventRegisteredFor').value = eventData.extendedProps.registered_for || ''
    document.getElementById('eventDescription').value = eventData.extendedProps.description || ''

    const startDate = new Date(eventData.start)
    const endDate = eventData.end ? new Date(eventData.end) : new Date(startDate.getTime() + 3600000)

    document.getElementById('eventStartDate').value = startDate.toISOString().split('T')[0]
    document.getElementById('eventStartTime').value = startDate.toTimeString().slice(0, 5)
    document.getElementById('eventEndTime').value = endDate.toTimeString().slice(0, 5)

    document.getElementById('eventRepeatType').value = ''
    document.getElementById('eventRepeatUntil').value = ''

    const weekdaySection = document.getElementById('weekdaySection')
    if (weekdaySection) {
      weekdaySection.style.display = 'none'
      document.querySelectorAll('.weekday-checkbox').forEach((cb) => {
        cb.checked = false
      })
    }

    const weekSummary = document.getElementById('weekSummary')
    if (weekSummary) weekSummary.textContent = ''

    toggleModal('eventModal', true)
    buildOccurrencesFromForm({ preview: true })
  }, 100)
}

// ======================= DELETE EVENT =======================
window.deleteEvent = function() {
  const eventData = calendar.getEventById(currentEventId)
  if (!eventData) return

  if (!checkPermission(eventData)) {
    showToast('error', 'Bạn không có quyền xóa sự kiện này.')
    return
  }

  closeDetailModal()
  toggleModal('confirmDeleteModal', true)
}

window.confirmDelete = async function() {
  closeConfirmDelete()

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

  if (!csrfToken) {
    showToast('error', 'Không tìm thấy CSRF token.')
    return
  }

  try {
    const formData = new FormData()
    formData.append('_method', 'DELETE')

    const response = await fetch(`/bookings/${currentEventId}`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: formData
    })

    let result
    const contentType = response.headers.get('content-type')

    if (contentType && contentType.includes('application/json')) {
      const text = await response.text()
      result = text ? JSON.parse(text) : {}
    } else {
      const text = await response.text()
      console.error('Non-JSON response:', response.status, text)

      showToast('error', `Lỗi server (${response.status}). Vui lòng kiểm tra log.`)
      return
    }

    if (!response.ok) {
      showToast('error', result.message || 'Không thể xóa sự kiện.')
      return
    }

    const action = result.action || 'deleted'
    const successMessage = action === 'cancelled'
      ? 'Lịch đã duyệt đã được chuyển sang trạng thái hủy.'
      : 'Đã xóa sự kiện thành công.'

    showToast('success', result.message || successMessage)
    closeDetailModal()

    await loadEvents()
    currentEventId = null

  } catch (err) {
    console.error('confirmDelete error:', err)
    showToast('error', 'Lỗi kết nối: ' + err.message)
  }
}

// ======================= UPDATE EVENT TIME =======================
async function updateEventTime(calendarEvent, infoCtx = null) {
  const id = calendarEvent.id
  const props = calendarEvent.extendedProps || {}

  const startDate = calendarEvent.start
  const endDate = calendarEvent.end || new Date(startDate.getTime() + 60 * 60 * 1000)

  const start = formatLocalDateTime(startDate)
  const end = formatLocalDateTime(endDate)

  const payload = {
    title: calendarEvent.title,
    category: props.category || 'work',
    lab_code: props.roomCode || null,
    description: props.description || '',
    start,
    end
  }

  if (!payload.lab_code) {
    showToast('error', 'Sự kiện không có thông tin phòng, không thể cập nhật thời gian.')
    if (infoCtx?.revert) infoCtx.revert()
    return
  }

  try {
    const response = await fetch(`/bookings/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(payload)
    })

    const text = await response.text()
    let result

    try {
      result = JSON.parse(text)
    } catch (e) {
      console.error('updateEventTime parse error:', response.status, text)
      showToast('error', 'Không thể cập nhật thời gian (response không phải JSON).')
      if (infoCtx?.revert) infoCtx.revert()
      return
    }

    if (!response.ok) {
      const msg = result?.message ||
        (result?.errors && Object.values(result.errors)[0]?.[0]) ||
        'Không thể cập nhật thời gian.'
      showToast('error', msg)
      if (infoCtx?.revert) infoCtx.revert()
      return
    }

    showToast('success', result.message || 'Đã cập nhật thời gian sự kiện.')
    await loadEvents()
  } catch (err) {
    console.error('updateEventTime error:', err)
    showToast('error', 'Lỗi kết nối khi cập nhật.')
    if (infoCtx?.revert) infoCtx.revert()
  }
}

// ======================= CONFLICT MODAL =======================
 function showConflictModal(conflicts) {
  const list = document.getElementById('conflictList')
  if (!list) return

   list.dataset.conflicts = JSON.stringify(conflicts)
  list.innerHTML = ''

  conflicts.forEach(c => {
    const li = document.createElement('li')
    li.innerHTML = `
      <b>${c.requested_start} → ${c.requested_end}</b><br>
      <span class="text-danger">
        Trùng với: ${c.conflict_with.title}
        (${c.conflict_with.start} → ${c.conflict_with.end})
      </span>
    `
    list.appendChild(li)
  })

  toggleModal('confirmConflictModal', true)
}

 window.confirmContinue = async function () {
  if (!pendingFormData) {
    closeConflictModal()
    return
  }

  const newFormData = new FormData()
  
  // Copy tất cả fields từ pendingFormData
  for (let [key, value] of pendingFormData.entries()) {
    newFormData.append(key, value)
  }
  
  // Thêm flag force
  newFormData.append('force', 'true')

  // Gửi lại request
  const res = await sendBookingRequest('/bookings', newFormData)
  
  if (!res.ok) {
    closeConflictModal()
    return
  }

  showToast('success', res.data?.message || 'Đã gửi yêu cầu đăng ký lịch.')
  closeConflictModal()
  closeModal()
  await loadEvents()
  pendingFormData = null
}
// ======================= INITIALIZATION =======================
document.addEventListener('DOMContentLoaded', function () {
  initDataMaps()
  initCalendar()
})
